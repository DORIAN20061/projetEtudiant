<?php
require_once 'Class/Database.php';
require_once 'Class/Versement.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = $_POST['matricule'] ?? null;
    $montant = $_POST['montant'] ?? null;
    $date_versement = $_POST['date_versement'] ?? date('Y-m-d');
    $numero_transaction = $_POST['numero_transaction'] ?? null;

    // Validation des champs obligatoires
    if (!$matricule || !$montant || !$numero_transaction) {
        die("Tous les champs obligatoires doivent être remplis.");
    }

    // Création d'un nouvel objet Versement
    $versement = new Versement($db);
    $versement->matricule = $matricule;
    $versement->date_versement = $date_versement;
    $versement->montant = $montant;
    $versement->numero_versement = $numero_transaction;

    $success = $versement->ajouterVersement();
    $message = $success
        ? "Versement ajouté avec succès."
        : "Une erreur s'est produite lors de l'ajout du versement.";
       

    // Mise à jour des montants dans la table `etudiants`
    $update_sql = "UPDATE etudiants 
                   SET reste = reste - ?, montant_paye = montant_paye + ? 
                   WHERE matricule = ?";
    $update_stmt = $db->prepare($update_sql);
    $update_stmt->bind_param("dds", $montant, $montant, $matricule);
    $update_stmt->execute();
    $update_stmt->close();

    // Récupérer les valeurs actuelles (reste et montant)
    $select_sql = "SELECT reste, montant FROM etudiants WHERE matricule = ?";
    $select_stmt = $db->prepare($select_sql);
    $select_stmt->bind_param("s", $matricule);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    $row = $result->fetch_assoc();
    $select_stmt->close();

    if ($row) {
        $reste = $row['reste'];
        $montant_total = $row['montant'];

        // Mise à jour du statut en fonction des valeurs
        $statut = ($reste <= 0) ? "Solvable" : (($reste <= $montant_total / 2) ? "En cours" : "Insolvable");

        $update_statut_sql = "UPDATE etudiants SET statut = ? WHERE matricule = ?";
        $statut_stmt = $db->prepare($update_statut_sql);
        $statut_stmt->bind_param("ss", $statut, $matricule);
        $statut_stmt->execute();
        $statut_stmt->close();
    }

    // Envoi de l'email
    $fin = $versement->envoyerMail($matricule, $montant);
    $message .= $fin ? " Email envoyé avec succès." : " Erreur lors de l'envoi du mail.";
}

// Récupération des matricules pour le formulaire
$query = "SELECT matricule FROM etudiants";
$result = $db->query($query);

if ($result === false) {
    die("Erreur dans la requête SQL : " . $db->error);
}

// Calcul du numéro de transaction
$sql1 = "SELECT COUNT(*) AS total_versements FROM versements";
$result1 = $db->query($sql1);
$totalVersements = ($result1 && $result1->num_rows > 0) ? $result1->fetch_assoc()['total_versements'] : 0;
$annee = date('y');
$month = date('m');
$day = date('d');
$numeroTransaction = sprintf('%02d%02d%02dV%04d', $annee, $month, $day, $totalVersements + 1);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un versement</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg,#7f4922, #000000);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #3f3737;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 900px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: white;
        }

        .form {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 500;
            margin-bottom: 5px;
            color: white ;
        }

        input,
        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        input:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 10px 30px rgba(102, 166, 255, 0.5);
        }

        select:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 10px 30px rgba(102, 166, 255, 0.5);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #2f80ed;
            box-shadow: 0 0 5px rgba(47, 128, 237, 0.5);
        }

        .form-actions {
            grid-column: span 3;
            display: flex;
            justify-content: right;

        }

        button {
            padding: 12px 20px;
            font-size: 16px;
            background: #2f80ed;
            color: #fff;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
            /* justify-content: right; */
        }

        button:hover {
            background: #1b5cb7;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 10px 30px rgba(102, 166, 255, 0.5);
        }

        .form-actions a button {
            background-color: wheat;
            color: black;
        }

        .form-actions a button:hover {
            background: red;

        }

        @media (max-width: 600px) {
            .form {
                grid-template-columns: 1fr;
            }
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
            max-width: 400px;
            width: 90%;
        }

        .popup-content h3 {
            margin: 0 0 10px;
        }

        .popup-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 5px;
        }

        .popup-actions .close {
            background: #2196F3;
            color: #fff;
        }

        .popup-actions .close:hover {
            background: #1769aa;
        }
    </style>
    <script>
        function generateMatricule() {
            const niveau = document.getElementById('niveau').value;
            if (niveau) {
                fetch(`index.php?niveau=${niveau}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('matricule').value = data;
                    })
                    .catch(error => console.error('Erreur:', error));

                // Mise à jour du montant en fonction du niveau
                const montantInput = document.getElementById('montant');
                if (niveau === 'B1') {
                    montantInput.value = 1000000;
                } else if (niveau === 'B2') {
                    montantInput.value = 2000000;
                } else if (niveau === 'B3') {
                    montantInput.value = 3000000;
                }
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Ajouter un Versement</h1>
        <form action="AjouterVer.php" method="POST" enctype="multipart/form-data" class="form">
           
            <div class="form-group">
                <label for="matricule">Matricule:</label>
                <select id="matricule" name="matricule" required>
                    <option value="">-- Sélectionnez le matricule --</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['matricule']) ?>">
                            <?= htmlspecialchars($row['matricule']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
           
            <div class="form-group">
                <label for="montant">Montant verser:</label>
                <input type="number" id="montant" name="montant" required>
            </div>
            <div class="form-group">
                <label for="date_versement">Date de Versement:</label>
                <input
                    type="date"
                    id="date_versement"
                    name="date_versement"
                    value="<?= date('Y-m-d') ?>"
                    readonly required style="background-color: #f9f9f9; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="numero_transaction">Numéro de Transaction:</label>
                <input
                    type="text"
                    id="numero_transaction"
                    name="numero_transaction"
                    value="<?= htmlspecialchars($numeroTransaction) ?>"
                    readonly
                    required style="background-color: #f9f9f9; cursor: not-allowed;">
            </div>
            <div class="form-actions">
                <button type="submit">Ajouter un versement</button>
                <a href="gestionEtu.php"><button>Annuler</button></a>
            </div>



        </form>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div id="popup" class="popup-overlay">
            <div class="popup-content">
                <h3><?= htmlspecialchars($message); ?></h3>
                <div class="popup-actions">
                    <button class="close" onclick="closePopup()">Fermer</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function closePopup() {
            const popup = document.getElementById('popup');
            popup.style.display = 'none';
        }
    </script>
</body>

</html>