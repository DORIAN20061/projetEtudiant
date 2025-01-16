<?php
require_once 'Class/Database.php';
require_once 'Class/Versement.php';
require_once 'Class/Matiere.php';

// // Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Récupération des enseignants
$query = "SELECT matricule, nom, prenom FROM enseignants";
$result = $db->query($query);

if ($result === false) {
    die("Erreur dans la requête SQL : " . $db->error);
}

// Gestion du formulaire
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        // Validation des données
        $nom_prof = htmlspecialchars(trim($_POST['nom_prof']));
        $matricule_prof = htmlspecialchars(trim($_POST['matricule_prof']));
        $nom_matiere = htmlspecialchars(trim($_POST['nom_matiere']));
        $nom_matiere = str_replace(' ', '_', $nom_matiere);
        $niveau_matiere = htmlspecialchars(trim($_POST['niveau_matiere']));

        if (empty($nom_prof) || empty($matricule_prof) || empty($nom_matiere) || empty($niveau_matiere)) {
            throw new Exception("Tous les champs sont obligatoires.");
        }

        // Création d'une nouvelle matière
        $matiere = new Matiere($db);
        $matiere->matricule_prof = $matricule_prof;
        $matiere->nom_prof = $nom_prof;
        $matiere->nom_matiere = $nom_matiere;
        $matiere->niveau_matiere = $niveau_matiere;

        // Ajout dans la base de données
        // if ($matiere->ajouterMatiere()) {
        //     $message = "Matière ajoutée avec succès.";
        // } else {
        //     throw new Exception("Erreur lors de l'ajout de la matière.");
        // }

        $success = $matiere->ajouterMatiere();
        if ($success) {
            $message = "Matiere ajouté .";

            $query1="ALTER TABLE ".$niveau_matiere." ADD COLUMN ".$nom_matiere." VARCHAR(60) DEFAULT 0, "." ADD COLUMN ".$nom_matiere."_CC VARCHAR(60) DEFAULT 0";
            $result1=$db->query($query1);

//             ALTER TABLE etudiants
// ADD COLUMN montant_paye BIGINT DEFAULT 0,
// ADD COLUMN reste BIGINT,
// ADD COLUMN statut VARCHAR(20),
// ADD COLUMN date_naissance DATE;
                

         
        } else {
            throw new Exception("Erreur lors de l'ajout de l'étudiant.");
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'une Matière</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background:linear-gradient(135deg, #ab601b, #0a0d10);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 900px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
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
            color: #555;
        }

        input,
        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        input:hover,
        select:hover {
            transform: translateY(-10%);
            transition: ease-in-out 0.5s;
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
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
        }

        button:hover {
            background: #1b5cb7;
            transform: translateY(-10%);
            transition: ease-in-out 0.5s;
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
</head>

<body>
    <div class="container">
        <h1>Ajouter une Matière</h1>
        <form action="AjouterMati.php" method="POST" class="form">
            <div class="form-group">
                <label for="nom_prof">Nom professeur :</label>
                <select id="nom_prof" name="nom_prof" required onchange="updateMatricule()">
                    <option value="">-- Sélectionnez le professeur --</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['nom']) ?> <?= htmlspecialchars($row['prenom']) ?>"
                            data-matricule="<?= htmlspecialchars($row['matricule']) ?>">
                            <?= htmlspecialchars($row['nom']) ?> <?= htmlspecialchars($row['prenom']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="matricule_prof">Matricule du Professeur :</label>
                <input type="text" id="matricule_prof" name="matricule_prof" readonly>
            </div>

            <div class="form-group">
                <label for="nom_matiere">Nom de la Matière :</label>
                <input type="text" id="nom_matiere" name="nom_matiere" required>
            </div>

            <div class="form-group">
                <label for="niveau_matiere">Niveau :</label>
                <select id="niveau_matiere" name="niveau_matiere" required>
                    <option value="">-- Sélectionnez le niveau --</option>
                    <option value="B1">B1</option>
                    <option value="B2">B2</option>
                    <option value="B3">B3</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit">Ajouter la matière</button>
                <a href="gestionEtu.php"><button type="button">Annuler</button></a>
            </div>
        </form>
    </div>

    <?php if (!empty($message)): ?>
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

        function updateMatricule() {
            const select = document.getElementById('nom_prof');
            const selectedOption = select.options[select.selectedIndex];
            const matricule = selectedOption.getAttribute('data-matricule');
            document.getElementById('matricule_prof').value = matricule || '';
        }
    </script>
</body>

</html>
