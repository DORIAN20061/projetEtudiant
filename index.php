<?php
require_once 'Class/Database.php';
require_once 'Class/Etudiant.php';

$database = new Database();
$db = $database->getConnection();
$etudiantObj = new Etudiant($db);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = trim($_POST['matricule'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($matricule) && !empty($password)) {
        if ($matricule === 'Admin' && $password === 'admin') {
            // Rediriger l'administrateur vers la page de gestion des étudiants
            header("Location: gestionEtu.php");
            exit();
        } else {
            // Vérifier les informations de connexion pour un étudiant
            $etudiant = $etudiantObj->verifierConnexion($matricule, $password);
            if ($etudiant) {
                if ($etudiant['first_login'] == 1) {
                    // Rediriger vers la page de modification du mot de passe
                    header("Location: etudiant/modifierPass.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                } else {
                    // Rediriger vers la page d'information de l'étudiant
                    header("Location: informationEtudiant.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                }
            } else {
                $message = "Matricule ou mot de passe incorrect.";
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
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
            background: linear-gradient(135deg, rgb(221, 120, 5), rgb(10, 13, 17));
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.2); /* Transparency */
            color: #fff;
            padding: 48px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px); /* Blur effect */
            animation: fadeIn 2s ease-in-out; /* Animation */
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .login-container h1 {
            text-align: center;
            margin-bottom: 120px;
            color: #fff;
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
            background-color: rgb(12, 34, 230);
            color: white;
            border: none;
            border-radius: 20px;
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

        .error {
            display: flex;
            margin: 10px;
            padding: 10px;
            justify-content: center;
        }

        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.5s ease;
        }

        .spinner {
            border: 12px solid rgba(255, 255, 255, 0.3);
            border-top: 12px solid #fff;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Connexion</h1>
        <?php if (!empty($message)): ?>
            <div class="error">
            <img src="uploads/no-data.gif" alt=""><p class="error"><?= afficherMessage($message); ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="matricule" placeholder="Matricule" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>

    <div id="loader">
        <div class="spinner"></div>
    </div>

    <script>
        // Affiche le loader lorsque la page se charge
        window.addEventListener('load', function () {
            const loader = document.getElementById('loader');
            loader.style.opacity = '0';
            setTimeout(function() {
                loader.style.display = 'none';
            }, 500); // Correspond à la durée de la transition en CSS
        });

        // Affiche le loader lors de la soumission du formulaire
        document.querySelector('form').addEventListener('submit', function () {
            document.getElementById('loader').style.display = 'flex';
            document.getElementById('loader').style.opacity = '1';
        });
    </script>
</body>

</html>