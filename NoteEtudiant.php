<?php
require_once 'Class/Database.php';
require 'fpdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection(); // Assurez-vous que la classe Database retourne un objet mysqli

$niveau = strval($_GET['niveau']);
$matricule = strval($_GET['matricule']);
// Classes disponibles
$classes = [$niveau];
$notes = [];

try {
    foreach ($classes as $classe) {
        $table_notes = strtolower($classe); // Assurez-vous que les tables existent
        $query = "SELECT * FROM `$table_notes`"; // Ajout des backticks pour les noms de table

        $result = $db->query($query);

        if ($result) {
            $notes[$classe] = [];
            while ($row = $result->fetch_assoc()) {
                $notes[$classe][] = $row;
            }
        } else {
            throw new Exception("Erreur lors de la récupération des notes pour la classe $classe: " . $db->error);
        }
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}

// Fonction pour récupérer les notes d'un étudiant
function getStudentNotes($conn, $matricule, $classe)
{
    $table = strtolower($classe);
    $query = "SELECT * FROM `$table` WHERE matricule = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }

    $stmt->bind_param('s', $matricule);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

// Fonction pour calculer la moyenne générale
function calculateAverage($notes)
{
    $sum = 0;
    $count = 0;
    foreach ($notes as $key => $value) {
        if (!in_array($key, ['id', 'matricule_etudiant', 'nom', 'prenom'])) {
            $sum += $value;
            $count++;
        }
    }
    return $count > 0 ? $sum / $count : 0;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Notes</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            background: black;
            color: white;
        }

        .sidebar {
            background-color: rgb(66, 37, 4);
            height: 93vh;
            position: fixed;
            width: 250px;
            padding: 20px;
            border-radius: 10px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar.visible {
            transform: translateX(0);
        }

        .sidebar h2 {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            margin: 37px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sidebar a:hover {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .sidebar .etu {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
        }

        .hamburger-menu {
            display: flex;
            flex-direction: column;
            cursor: pointer;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
        }

        .hamburger-menu .bar {
            height: 3px;
            width: 25px;
            background-color: white;
            margin: 4px 0;
            transition: 0.4s;
        }

        .main-content {
            margin-left: 300px;
            margin-top: 20px;
            padding: 20px;
            width: calc(100% - 300px);
            text-align: center;
        }

        .table-card {
            margin-bottom: 30px;
        }

        .table-card h2 {
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #66a6ff;
            color: #fff;
        }

        .table-container {
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: #000;
        }
    </style>
</head>

<body>

    <div class="hamburger-menu" onclick="toggleSidebar()">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>

    <div class="sidebar">
        <h2>Menu</h2>
        <?php if (!empty($matricule)): ?>
            <a href="informationEtudiant.php?matricule=<?= htmlspecialchars($matricule); ?>">Mes Informations</a>
        <?php endif; ?>
        <?php if (!empty($matricule)): ?>
            <a href="MatiereEtu.php?niveau=<?= htmlspecialchars($niveau); ?>&&matricule=<?= htmlspecialchars($matricule); ?>">Mes Matières</a>
        <?php endif; ?>
        <?php if (!empty($matricule)): ?>
            <a class="etu" href="NoteEtudiant.php?niveau=<?= htmlspecialchars($niveau); ?>&&matricule=<?= htmlspecialchars($matricule); ?>">Mes Notes</a>
        <?php endif; ?>
        <a href="index.php">Déconnexion</a>
    </div>

    <main class="main-content">
        <?php foreach ($classes as $classe): ?>
            <?php if (isset($notes[$classe]) && !empty($notes[$classe])): ?>
                <div class="table-card">
                    <div class="table-container">
                        <h2>Vos Notes en <?php echo htmlspecialchars($classe); ?></h2>
                        <table>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <?php foreach (array_keys($notes[$classe][0]) as $column): ?>
                                    <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                        <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $column))); ?></th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($notes[$classe] as $student): ?>
                                <tr>
                                    <?php if (!empty($matricule)): ?>
                                        <?php if ($student['matricule'] === $matricule): ?>
                                            <td><?php echo htmlspecialchars($student['matricule'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['nom'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($student['prenom'] ?? ''); ?></td>
                                            <?php foreach ($student as $column => $value): ?>
                                                <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                                    <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <p>Aucune note disponible pour la classe <?php echo htmlspecialchars($classe); ?>.</p>
            <?php endif; ?>
        <?php endforeach; ?>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('visible');
        }
    </script>
</body>

</html>
