<?php
require_once 'Class/Database.php';
require 'fpdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection(); // Assurez-vous que la classe Database retourne un objet mysqli

// Classes disponibles
$classes = ['B1', 'B2', 'B3'];
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
    $table = "note_" . strtolower($classe);
    $query = "SELECT * FROM `$table` WHERE matricule_etudiant = ?";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Liste des Notes</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color:rgb(7, 7, 14);
        }

        .sidebar {
            background-color: rgb(44, 25, 3);
            color: white;
            width: 160px;
            padding: 20px 10px;
            position: fixed;
            top: 0;
            bottom: 0;
            height: 100vh;
            border-radius: 10px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }


            .hamburger-menu {
        display: block;
        flex-direction: column;
        cursor: pointer;
        position: fixed;
        top: 20px;
        right: 1370px; /* Positionner à droite de la sidebar */
        z-index: 1001;
    }

    .hamburger-menu .bar {
        height: 3px;
        width: 25px;
        background-color: white;
        margin: 4px 0;
        transition: 0.4s;
    }
 
        .sidebar a {
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
            border-radius: 66px;
            transition: all 0.3s ease;
        
        }

        .sidebar a:hover {
            background-color:rgb(209, 142, 115);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(245, 165, 90, 0.5);
        }

        .sidebar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .sidebar .etu {
            background-color:rgb(87, 70, 64);
        }

        .main-content {
            margin-left: 180px;
            margin-top: 20px;
            padding: 20px;
            width: calc(100% - 180px);
            text-align: center;
        }

        .table-card {
            margin-bottom: 30px;
            
        }

        .table-card h2 {
            color: #4a4a4a;
        }

        .send-notes-button {
            background-color:rgb(27, 121, 228);
            color: white;
            border: none;
            border-radius: 19px;
            padding: 10px 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .send-notes-button:hover {
            background-color: #357abd;
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
            background-color:rgb(24, 28, 34);
            color: #fff;
        }

        .table-container {
            margin-top: 20px;
            background-color: white;
            border-radius: 30px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
     <!-- Hamburger Menu -->
     <div class="hamburger-menu" onclick="toggleSidebar()">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="gestionEtu.php"> <i class="fas fa-user-graduate"></i> Étudiants </a>
        <a  href="gestionVer.php"><i class="fas fa-money-bill-wave"></i> Versements</a>
        <a  href="gestionEnsei.php"><i class="fas fa-chalkboard-teacher"></i> Enseignant</a>
        <a href="statistiques.php"> <i class="fas fa-chart-bar"></i> Statistiques</a>
        <a href="gestionMati.php"> <i class="fas fa-book"></i>  Matieres</a>
        <a class="etu" href="listeNote.php"> <i class="fas fa-graduation-cap"></i> Notes</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
    <main class="main-content">

        <?php foreach ($classes as $classe): ?>
            <?php if (isset($notes[$classe]) && !empty($notes[$classe])): ?>
                <div class="table-card">
                    <div class="table-container">
                        <h2>Notes des étudiants de la classe <?php echo htmlspecialchars($classe); ?></h2>
                        <button class="send-notes-button" onclick="window.location.href='envoyer_notes.php?classe=<?php echo urlencode($classe); ?>'">Envoyer les notes</button>
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
                                    <td><?php echo htmlspecialchars($student['matricule'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($student['nom'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($student['prenom'] ?? ''); ?></td>
                                    <?php foreach ($student as $column => $value): ?>
                                        <?php if (!in_array($column, ['id', 'matricule', 'nom', 'prenom'])): ?>
                                            <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
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
    
</body>

</html>