<?php
require_once 'Class/Database.php';
require_once 'Class/Etudiant.php';
require_once 'Class/Enseignant.php';

$database = new Database();
$db = $database->getConnection();
$etudiantObj = new Enseignant($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = trim($_POST['matricule'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // if ($matricule==="ENS-20241227110838" && $password==="ENS-20241227110838") {
    //     header("Location: infoProf.php?matricule=" . $matricule);
    //     exit();
    // }
    // $statut = trim($_POST['statut'] ?? '');

    // if ($statut === "Etudiant") {
        //Vérifier les informations de connexion pour un étudiant
        $etudiant = $etudiantObj->verifierConnexion($matricule, $password);
        if ($etudiant) {
            header("Location: infoProf.php?matricule=" . $matricule);
             exit();
        } else {
            $message = "Matricule ou mot de passe incorrect.";
        }
    // } elseif ($statut === "Admin") {
    //     // Vérifier les informations de connexion pour un admin
    //     if ($matricule === "Admin" && $password === "admin") {
    //         header("Location: gestionEtu.php");
    //         exit();
    //     } else {
    //         $message = "Matricule ou mot de passe incorrect pour l'administrateur.";
    //     }
    // } 
    // else {
    //     $message = "Statut invalide. Veuillez réessayer.";
    //  }
}

// Fonction pour afficher un message en toute sécurité
function afficherMessage($message)
{
    return htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #ffffff;
            color: #333;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .login-container input:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .login-container input:focus {
            outline: none;
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        .login-container button {
            padding: 12px;
            background-color: #6a11cb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-container button:hover {
            background-color: #2575fc;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .login-container .error {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            padding: 10px;
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid #e74c3c;
            border-radius: 5px;
        }

        @media (max-width: 500px) {
            .login-container {
                padding: 20px;
            }

            .login-container h2 {
                font-size: 20px;
            }

            .login-container button {
                font-size: 14px;
            }
        }

        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin: 15px 0;

        }
        img {
            max-width: 50px;
            /* Réduit la taille de l'image */
            max-height: 50px;
            object-fit: cover;
            border-radius: 50px;
            width: 100%;
        }

        select:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }
        .error{
            /* display: flex; */
            display: flex;
            margin: 10px;
            padding: 10px;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Connexion</h2>
        <?php if (!empty($message)): ?>
            <div class="error">
            <img src="uploads/no-data.gif" alt=""><p class="error"><?= afficherMessage($message); ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <select id="statut" name="statut" required>
                <option value="">-- Sélectionnez le statut --</option>
                <option value="Admin">Admin</option>
                <option value="Etudiant">Étudiant</option>
            </select>
            <input type="text" name="matricule" placeholder="Matricule" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>

</html>
