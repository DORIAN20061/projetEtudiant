<?php
require_once 'Class/Database.php';
require_once 'Class/Matiere.php';

$database = new Database();
$db = $database->getConnection();
$matiere = new Matiere($db);

// Recherche
$niveau = strval($_GET['niveau']);
$matricule = strval($_GET['matricule']);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$matieres = $matiere->getAllMatiereParEtu($niveau);

// Suppression
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $matiere = $matiere->supprimerMatiere($id);
    if ($matiere) {
        $message = "L'étudiant a été supprimé avec succès.";
        header("Location: gestionMati.php");
        exit();
    } else {
        $message = "Une erreur s'est produite lors de la suppression.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Matieres</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #191a1c;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: rgb(44, 25, 3);
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.1);
            border-radius: 0 10px 10px 0;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar.visible {
            transform: translateX(0);
        }

        .sidebar h2 {
            margin: 20px 0;
            font-size: 1.5em;
            text-align: center;
            color: #ffffff;
        }

        .sidebar a {
            text-decoration: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .sidebar a:hover {
            background: linear-gradient(135deg, #74ebd5, rgb(141, 77, 17));
            transform: translateY(-2px);
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

        .content {
            max-width: 900px;
            background: #ffffff;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-left: 300px;
            color: #000;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            margin: 0;
            font-size: 1.5em;
            color: #000;
        }

        form {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        input[type="text"] {
            padding: 5px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button[type="submit"] {
            padding: 5px 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #4caf50;
            color: white;
            cursor: pointer;
            margin-left: 10px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: linear-gradient(135deg, #695b5b, #f5f5f5);
            color: white;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
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
        }

        .popup-actions {
            margin-top: 20px;
        }

        .popup-actions button {
            padding: 5px 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup-actions .close {
            background-color: #e74c3c;
            color: white;
        }

        .popup-actions .confirm {
            background-color: #4caf50;
            color: white;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <div class="hamburger-menu" onclick="toggleSidebar()">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="informationEtudiant.php?matricule=<?= htmlspecialchars($matricule); ?>">Mes Informations</a>
        <a class="etu" href="MatiereEtu.php?niveau=<?= htmlspecialchars($niveau); ?>&&matricule=<?= htmlspecialchars($matricule); ?>">Mes Matières</a>
        <a href="NoteEtudiant.php?niveau=<?= htmlspecialchars($niveau); ?>&&matricule=<?= htmlspecialchars($matricule); ?>">Mes Notes</a>
        <a href="index.php">Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="header">
            <h2>Vos matières</h2>
            <form method="GET" action="gestionVer.php">
                <input name="search" type="text" placeholder="Rechercher..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <?php if (!empty($message)): ?>
            <p style="color: green;"> <?= htmlspecialchars($message); ?> </p>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Matricule Professeur</th>
                        <th>Nom professeur</th>
                        <th>Nom de Matiere</th>
                        <th>Niveau Matiere</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($matieres)): ?>
                        <?php foreach ($matieres as $matiere): ?>
                            <tr>
                                <td><?= htmlspecialchars($matiere['id']); ?></td>
                                <td><?= htmlspecialchars($matiere['matricule_prof']); ?></td>
                                <td><?= htmlspecialchars($matiere['nom_prof']); ?></td>
                                <td><?= str_replace('_',' ',htmlspecialchars($matiere['nom_matiere'])); ?></td>
                                <td><?= htmlspecialchars($matiere['niveau_matiere']); ?></td>
                                <td class="actions">
                                    <button class="details" onclick="location.href='#?niveau=<?= $matiere['niveau_matiere']; ?>&&matiere=<?= $matiere['nom_matiere']; ?>'">
                                        Voir le cours
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Aucune matière trouvée.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Popup -->
    <div id="popup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <h3>Confirmation</h3>
            <p>Êtes-vous sûr de vouloir supprimer cet étudiant ?</p>
            <div class="popup-actions">
                <button class="cancel" onclick="closePopup()">Annuler</button>
                <button class="confirm" id="confirm-delete">Confirmer</button>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('visible');
        }

        function openPopup(deleteId) {
            const popup = document.getElementById('popup');
            popup.style.display = 'flex';
            const confirmButton = document.getElementById('confirm-delete');
            confirmButton.onclick = function() {
                location.href = '?delete_id=' + deleteId;
            };
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }
    </script>
</body>

</html>
