<?php
require_once '../Class/Database.php';
$message = '';

// Connexion à la base de données avec mysqli
$database = new Database();
$db = $database->getConnection(); // Vérifiez que cette méthode retourne un objet mysqli

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $matricule = strVal($_POST['matricule']); // Récupération sécurisée de l'ID via le formulaire

    if (empty($password)) {
        $message = "Le mot de passe ne peut pas être vide.";
    } elseif ($matricule === "") {
        $message = "ID utilisateur invalide.";
    } else {
        // Hashage du mot de passe
        // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Modifier le mot de passe dans la table "connexion"
        $query = "UPDATE connexion SET password = ? WHERE matricule = ?";
        if ($stmt = $db->prepare($query)) {
            $stmt->bind_param('ss', $password, $matricule);

            if ($stmt->execute()) {
                $message = "Mot de passe modifié avec succès.";
                // Redirection après succès
                header("Location: ../informationEtudiant.php?matricule=" . $matricule);
                exit();
            } else {
                $message = "Une erreur s'est produite lors de la modification : " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "Erreur de préparation de la requête : " . $db->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Mot de passe</title>
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

        .login-container h2 {
            margin-bottom: 40px;
            font-size: 24px;
            color: #fff;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .login-container input {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .login-container input:focus {
            outline: none;
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        .login-container input:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .login-container button {
            padding: 12px;
            font-size: 16px;
            background: #0c22e6;
            color: #fff;
            border: none;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-container button:hover {
            background: #ff3d71;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .login-container .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }

        .login-container .message.success {
            background: #4caf50;
            color: #fff;
        }

        .login-container .message.error {
            background: #e74c3c;
            color: #fff;
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
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Modifier Mot de passe</h2>
        <?php if (!empty($message)): ?>
            <p class="message <?= strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
                <?= htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
        <form method="POST" action="modifierPass.php">
            <input type="hidden" name="matricule" value="<?= isset($_GET['matricule']) ? strval($_GET['matricule']) : 0; ?>"> <!-- Transmettre l'ID -->
            <input type="password" name="password" placeholder="Nouveau mot de passe" required>
            <button type="submit">Modifier</button>
        </form>
    </div>
</body>

</html>
