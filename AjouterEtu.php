<?php
require_once 'Class/Database.php';
require_once 'Class/Etudiant.php';
require_once 'Class/B1.php';
require_once 'Class/B2.php';
require_once 'Class/B3.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Récupération des données avec validation
        $nom = htmlspecialchars(trim($_POST['nom']));
        $prenom = htmlspecialchars(trim($_POST['prenom']));
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ?: '';
        $matricule = htmlspecialchars(trim($_POST['matricule']));
        $niveau = htmlspecialchars(trim($_POST['niveau']));
        $montant = filter_var($_POST['montant'], FILTER_VALIDATE_INT);
        $nom_parent = htmlspecialchars(trim($_POST['nom_parent']));
        $email_parent = filter_var($_POST['email_parent'], FILTER_VALIDATE_EMAIL) ?: '';
        $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
        $montant_paye = 0;
        $reste = filter_var($_POST['reste'], FILTER_VALIDATE_INT);
        $statut = "Insolvable";
        $date_naissance = htmlspecialchars(trim($_POST['date_naissance']));

        if (!$email || !$email_parent) {
            throw new Exception("Email invalide.");
        }

        // Gestion de la photo
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $photo_name = basename($_FILES['photo']['name']);
        $photo_path = $upload_dir . $photo_name;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            throw new Exception("Erreur lors de l'upload de la photo.");
        }

        // Création de l'étudiant
        $etudiant = new Etudiant($db);
        $b1= new B1($db);
        $b2= new B2($db);
        $b3= new B3($db);

        $b1->matricule=$matricule;
        $b1->nom=$nom;
        $b1->prenom=$prenom;

        $b2->matricule=$matricule;
        $b2->nom=$nom;
        $b2->prenom=$prenom;

        $b3->matricule=$matricule;
        $b3->nom=$nom;
        $b3->prenom=$prenom;


        $etudiant->nom = $nom;
        $etudiant->prenom = $prenom;
        $etudiant->matricule = $matricule;
        $etudiant->photo = $photo_name;
        $etudiant->email = $email;
        $etudiant->niveau = $niveau;
        $etudiant->montant = $montant;
        $etudiant->nom_parent = $nom_parent;
        $etudiant->email_parent = $email_parent;
        $etudiant->age = $age;
        $etudiant->montant_paye =$montant_paye;
        $etudiant->reste = $reste;
        $etudiant->statut = $statut;
        $etudiant->date_naissance = $date_naissance;

        if ($niveau==="B1") {
            $success = $b1->ajouterEtudiant();
        }
        elseif ($niveau === "B2") {
            $success = $b2->ajouterEtudiant();
        }
        elseif ($niveau === "B3") {
            $success = $b3->ajouterEtudiant();
        }



        // Ajout de l'étudiant et envoi du matricule par email
        $success = $etudiant->ajouterEtudiant() && $etudiant->ajouterEtudiantCon();
        if ($success) {
            $repond = $etudiant->envoyerMatriculeParEmail($matricule, $email, $nom);
            $message = $repond
                ? "Étudiant ajouté et email envoyé avec succès."
                : "Étudiant ajouté, mais erreur lors de l'envoi de l'email.";

         
        } else {
            throw new Exception("Erreur lors de l'ajout de l'étudiant.");
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['niveau'])) {
    try {
        $niveau = htmlspecialchars($_GET['niveau']);
        $database = new Database();
        $db = $database->getConnection();

        // Récupération du nombre d'étudiants dans le niveau
        $query = "SELECT COUNT(*) AS count FROM etudiants WHERE niveau = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $niveau);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'] + 1;

        // Génération du matricule
        $currentYear = date("Y");
        $matricule = $currentYear . $niveau . str_pad($count, 3, "0", STR_PAD_LEFT);

        echo htmlspecialchars($matricule);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo "Erreur : " . $e->getMessage();
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'un étudiant</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg,rgb(11, 16, 19),rgb(66, 30, 7));
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: black;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 900px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: rgb(245, 243, 241);
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
            border-radius: 21px;
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
                fetch(`AjouterEtu.php?niveau=${niveau}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('matricule').value = data;
                    })
                    .catch(error => console.error('Erreur:', error));

                // Mise à jour du montant en fonction du niveau
                const montantInput = document.getElementById('montant');
                const resteInput = document.getElementById('reste');
                if (niveau === 'B1') {
                    montantInput.value = 1000000;
                    resteInput.value = 1000000;
                } else if (niveau === 'B2') {
                    montantInput.value = 2000000;
                    resteInput.value = 2000000;
                } else if (niveau === 'B3') {
                    montantInput.value = 3000000;
                    resteInput.value = 2000000;
                }
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Ajouter un étudiant</h1>
        <form action="AjouterEtu.php" method="POST" enctype="multipart/form-data" class="form">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="matricule">Matricule:</label>
                <input type="text" id="matricule" name="matricule" readonly required style="background-color: #f9f9f9; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="niveau">Niveau:</label>
                <select id="niveau" name="niveau" required onchange="generateMatricule()">
                    <option value="">-- Sélectionnez le niveau --</option>
                    <option value="B1">B1</option>
                    <option value="B2">B2</option>
                    <option value="B3">B3</option>
                </select>
            </div>
            <div class="form-group">
                    <label for="date_naissance">Date de Naissance:</label>
                    <input type="date" id="date_naissance" name="date_naissance" max="2011-12-31" required onchange="calculateAge()">
            </div>
            <div class="form-group">
                <label for="montant">Montant à payer:</label>
                <input type="number" id="montant" name="montant" readonly required style="background-color: #f9f9f9; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="reste">Reste à payer:</label>
                <input type="number" id="reste" name="reste" readonly required style="background-color: #f9f9f9; cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label for="nom_parent">Nom du parent:</label>
                <input type="text" id="nom_parent" name="nom_parent" required>
            </div>
            <div class="form-group">
                <label for="email_parent">Email du parent:</label>
                <input type="email" id="email_parent" name="email_parent" required>
            </div>
            <div class="form-group">
                <label for="age">Âge:</label>
                <input type="number" id="age" name="age" required readonly style="background-color:rgb(99, 93, 93); cursor: not-allowed;">
            </div>
            <div class="form-actions">
                <button type="submit">Ajouter l'étudiant</button>
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
    <script>
    function calculateAge() {
        const dateNaissanceInput = document.getElementById('date_naissance');
        const ageInput = document.getElementById('age');
        const dateNaissance = new Date(dateNaissanceInput.value);
        const today = new Date();

        let age = today.getFullYear() - dateNaissance.getFullYear();
        const monthDifference = today.getMonth() - dateNaissance.getMonth();
        if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dateNaissance.getDate())) {
            age--;
        }

        ageInput.value = age;
    }
</script>

</body>

</html>