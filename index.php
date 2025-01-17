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

    // Vérifier les identifiants pour l'administrateur
    if ($matricule === 'Admin' && $password === 'admin') {
        header("Location: gestionEtu.php");
        exit();
    }

    // Vérifier les informations de connexion pour un étudiant
    $etudiant = $etudiantObj->verifierConnexion($matricule, $password);
    if ($etudiant) {
        $sql = "SELECT statut FROM connexion WHERE matricule = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $stmt->bind_result($statut);
        $stmt->fetch();
        $stmt->close();
        $db->close();

        if ($statut) {
            if ($statut === 'Etudiant') {
                if ($matricule === $password) {
                    // Rediriger vers la page de modification du mot de passe
                    header("Location: etudiant/modifierPass.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                } else {
                    // Rediriger vers la page d'information de l'étudiant
                    header("Location: informationEtudiant.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                }
            } elseif ($statut === 'Administration') {
                header("Location: gestionEtu.php");
                exit();
            } elseif ($statut === 'Professeur') {
                if ($matricule === $password) {
                    // Rediriger vers la page de modification du mot de passe
                    header("Location: etudiant/modifierProf.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                } else {
                    // Rediriger vers la page d'information de l'étudiant
                    header("Location: infoProf.php?matricule=" . urlencode($etudiant['matricule']));
                    exit();
                }
            }
        } else {
            $message = "Matricule ou mot de passe incorrect.";
        }
    } else {
        $message = "Matricule ou mot de passe incorrect.";
    }
}

// Fonction pour afficher un message en toute sécurité
function afficherMessage($message)
{
    return htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e67e22 0%, #4a1c1c 100%);
        }

        .login-container {
            background-color: rgba(121, 85, 72, 0.8);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }

        .login-title {
            color: white;
            font-size: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .input-field {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            background: white;
        }

        .login-button {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 2rem;
            background-color: #6c2fff;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #5a1ee0;
        }

        .error {
            color: #e74c3c;
            background: rgba(231, 76, 60, 0.1);
            padding: 10px;
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid #e74c3c;
            border-radius: 5px;
        }

        img {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 50px;
            width: 100%;
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
        <h1 class="login-title">Connexion</h1>
        <?php if (!empty($message)): ?>
            <div class="error">
                <img src="uploads/no-data.gif" alt=""><p class="error"><?= afficherMessage($message); ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="matricule" class="input-field" placeholder="Matricule" required>
            <input type="password" name="password" class="input-field" placeholder="Mot de passe" required>
            <button type="submit" class="login-button">Se connecter</button>
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