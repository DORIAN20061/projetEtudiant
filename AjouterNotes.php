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
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter des Notes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6dd5fa, #2980b9);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.15);
            max-width: 1000px;
            width: 100%;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #d1ecf1;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
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
            z-index: 9999;
        }

        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 90%;
        }

        .popup-content h3 {
            margin-bottom: 15px;
        }

        .popup-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #28a745;
            color: #fff;
            cursor: pointer;
        }

        .popup-actions button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
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
                            <th><i class="fas fa-pen"></i> Ajouter une Note / 20</th>
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
    <!-- Close popup script -->
    <script>
        function closePopup() {
            const popup = document.getElementById('popup');
            popup.style.display = 'none';
        }
    </script>
</body>

</html>
