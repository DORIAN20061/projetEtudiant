<?php
require_once 'Class/Database.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

$niveau = strval($_GET['niveau']);
$matiere = strval($_GET['matiere']);
$matiere_cc = strval($_GET['matiere'])."_CC";

// Récupération des étudiants
$query = "SELECT id, nom, prenom, matricule, " . $matiere . ", ".$matiere_cc." FROM " . $niveau;
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$message = '';
// Traitement du formulaire pour ajouter des notes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['notes'] as $id_etudiant => $note) {
        $note = floatval($note); // Convertir la note en float
        $updateQuery = "UPDATE " . $niveau . " SET " . $matiere . " = ?  WHERE id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bind_param("di", $note, $id_etudiant);
        $updateStmt->execute();
    }
    foreach ($_POST['notes_cc'] as $id_etudiant => $note) {
        $note = floatval($note); // Convertir la note en float
        $updateQuery = "UPDATE " . $niveau . " SET " . $matiere_cc . " = ?  WHERE id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bind_param("di", $note, $id_etudiant);
        $updateStmt->execute();
    }
    $message = "Notes enregistrées avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter des Notes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Variables de thème */
        :root[data-theme="light"] {
            --bg-gradient-1: #6dd5fa;
            --bg-gradient-2: #2980b9;
            --container-bg: #ffffff;
            --text-color: #333333;
            --table-header-bg: #4CAF50;
            --table-header-color: white;
            --table-row-even: #f2f2f2;
            --table-row-hover: #d1ecf1;
            --input-bg: #ffffff;
            --input-text: #333333;
            --btn-primary-bg: #007bff;
            --btn-primary-hover: #0056b3;
            --popup-bg: #ffffff;
            --popup-overlay: rgba(0, 0, 0, 0.5);
            --success-btn-bg: #28a745;
            --success-btn-hover: #218838;
        }

        :root[data-theme="dark"] {
            --bg-gradient-1: #2c3e50;
            --bg-gradient-2: #1a1a1a;
            --container-bg: #2c3e50;
            --text-color: #ffffff;
            --table-header-bg: #1a1a1a;
            --table-header-color: #ffffff;
            --table-row-even: #34495e;
            --table-row-hover: #3498db;
            --input-bg: #34495e;
            --input-text: #ffffff;
            --btn-primary-bg: #3498db;
            --btn-primary-hover: #2980b9;
            --popup-bg: #2c3e50;
            --popup-overlay: rgba(0, 0, 0, 0.7);
            --success-btn-bg: #2ecc71;
            --success-btn-hover: #27ae60;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-1), var(--bg-gradient-2));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .container {
            background: var(--container-bg);
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.15);
            max-width: 1000px;
            width: 100%;
        }

        table th {
            background-color: var(--table-header-bg);
            color: var(--table-header-color);
        }

        table tr:nth-child(even) {
            background-color: var(--table-row-even);
        }

        table tr:hover {
            background-color: var(--table-row-hover);
        }

        .form-control {
            background-color: var(--input-bg);
            color: var(--input-text);
            border-color: var(--text-color);
        }

        .form-control:focus {
            background-color: var(--input-bg);
            color: var(--input-text);
        }

        .btn-primary {
            background-color: var(--btn-primary-bg);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--btn-primary-hover);
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--popup-overlay);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup-content {
            background: var(--popup-bg);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
            color: var(--text-color);
        }

        .popup-content h3 {
            margin-bottom: 15px;
        }

        .popup-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: var(--success-btn-bg);
            color: #fff;
            cursor: pointer;
        }

        .popup-actions button:hover {
            background-color: var(--success-btn-hover);
        }

        /* Style du bouton de thème */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: var(--container-bg);
            border: 2px solid var(--text-color);
            color: var(--text-color);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
        }

        .theme-toggle i {
            font-size: 20px;
            transition: transform 0.5s ease;
        }

        /* Styles pour les inputs en mode sombre */
        [data-theme="dark"] .form-control::placeholder {
            color: #ffffff80;
        }

        [data-theme="dark"] .table {
            color: var(--text-color);
        }
    </style>
</head>

<body>
    <!-- Bouton de thème -->
    <button id="themeToggle" class="theme-toggle" aria-label="Changer le thème">
        <i class="fas fa-sun"></i>
    </button>

    <div class="container">
        <h1 class="text-center mb-4">
            <i class="fas fa-edit"></i> Ajouter des Notes en <?= str_replace('_',' ',htmlspecialchars($matiere)); ?>
        </h1>
        <form method="POST" action="">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th><i class="fas fa-id-badge"></i> Matricule</th>
                            <th><i class="fas fa-user"></i> Nom</th>
                            <th><i class="fas fa-user"></i> Prénom</th>
                            <th><i class="fas fa-pen"></i> Ajouter une Note SN / 20</th>
                            <th><i class="fas fa-pen"></i> Ajouter une Note CC / 20</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['matricule']); ?></td>
                                <td><?= htmlspecialchars($row['nom']); ?></td>
                                <td><?= htmlspecialchars($row['prenom']); ?></td>
                                <td>
                                    <input type="number" name="notes[<?= $row['id']; ?>]" 
                                           class="form-control" 
                                           step="0.01" min="0" max="20" 
                                           value="<?= htmlspecialchars($row[$matiere]); ?>">
                                </td>
                                <td>
                                    <input type="number" name="notes_cc[<?= $row['id']; ?>]" 
                                           class="form-control" 
                                           step="0.01" min="0" max="20" 
                                           value="<?= htmlspecialchars($row[$matiere_cc]); ?>">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les Notes
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($message)): ?>
        <div id="popup" class="popup-overlay">
            <div class="popup-content">
                <h3>
                    <i class="fas fa-check-circle text-success"></i> <?= htmlspecialchars($message); ?>
                </h3>
                <div class="popup-actions">
                    <button onclick="closePopup()">
                        <i class="fas fa-times"></i> Fermer
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts pour le thème et le popup -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;
            const icon = themeToggle.querySelector('i');

            // Vérifier le thème sauvegardé
            const savedTheme = localStorage.getItem('theme') || 'light';
            html.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);

            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);

                // Animation de rotation
                icon.style.transform = `rotate(${newTheme === 'dark' ? '360deg' : '0deg'})`;
            });

            function updateThemeIcon(theme) {
                icon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
            }
        });

        function closePopup() {
            const popup = document.getElementById('popup');
            popup.style.display = 'none';
        }
    </script>
</body>

</html>