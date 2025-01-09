<?php
require_once 'Class/Database.php';
require_once 'Class/Etudiant.php';

// Activer l'affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database = new Database();
$db = $database->getConnection();
$etudiant = new Etudiant($db);

$message = '';

// Récupérer les informations de l'étudiant
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $etudiantInfo = $etudiant->getEtudiantById($id);
    if (!$etudiantInfo) {
        $message = "Étudiant non trouvé.";
    }
} else {
    header("Location: gestionEtu.php");
    exit();
}

// Mettre à jour les informations de l'étudiant
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $photo = $_FILES['photo']['name'];
    $date_naissance = trim($_POST['date_naissance']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $matricule = trim($_POST['matricule']);
    $email = trim($_POST['email']);
    $niveau = trim($_POST['niveau']);
    $montant = floatval($_POST['montant']);

    // Upload de la photo
    if (!empty($photo)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($photo);
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $message = "Erreur lors du téléchargement de la photo.";
        } else {
            $photo = basename($photo); // Utiliser le nom de fichier de la nouvelle photo
        }
    } else {
        $photo = $etudiantInfo['photo']; // Conserver l'ancienne photo si aucune nouvelle photo n'est téléchargée
    }

    if (empty($message) && $etudiant->updateEtudiant($id, $nom, $prenom, $matricule, $email, $niveau, $date_naissance, $montant, $photo)) {
        header("Location: gestionEtu.php");
        exit();
    } else {
        $message = "Une erreur s'est produite lors de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Étudiant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, rgb(221, 120, 5), rgb(10, 13, 17));
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 48px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
            animation: fadeIn 2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-container input:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .form-container input:focus {
            outline: none;
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        .form-container button {
            padding: 12px;
            background-color: rgb(12, 34, 230);
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-container button:hover {
            background-color: #2575fc;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .form-container .error {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            padding: 10px;
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid #e74c3c;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Modifier Étudiant</h1>
        <?php if (!empty($message)): ?>
            <div class="error"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($etudiantInfo['id']); ?>">
            <label for="photo">Photo:</label>
            <input type="file" name="photo" id="photo">
            <label for="date_naissance">Date de Naissance:</label>
            <input type="date" name="date_naissance" id="date_naissance" value="<?= htmlspecialchars($etudiantInfo['date_naissance']); ?>" required>
            <label for="nom">Nom:</label>
            <input type="text" name="nom" id="nom" placeholder="Nom" value="<?= htmlspecialchars($etudiantInfo['nom']); ?>" required>
            <label for="prenom">Prénom:</label>
            <input type="text" name="prenom" id="prenom" placeholder="Prénom" value="<?= htmlspecialchars($etudiantInfo['prenom']); ?>" required>
            <label for="matricule">Matricule:</label>
            <input type="text" name="matricule" id="matricule" placeholder="Matricule" value="<?= htmlspecialchars($etudiantInfo['matricule']); ?>" required>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($etudiantInfo['email']); ?>" required>
            <label for="niveau">Niveau:</label>
            <input type="text" name="niveau" id="niveau" placeholder="Niveau" value="<?= htmlspecialchars($etudiantInfo['niveau']); ?>" required>
            <label for="montant">Montant à Payer:</label>
            <input type="number" step="0.01" name="montant" id="montant" placeholder="Montant à Payer" value="<?= htmlspecialchars($etudiantInfo['montant']); ?>" required>
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</body>

</html>