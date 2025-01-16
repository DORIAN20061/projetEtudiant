<?php
require_once 'Class/Database.php';
require_once 'Class/Matiere.php';

$database = new Database();
$db = $database->getConnection();
$matiere = new Matiere($db);

// Recherche
$matricule = isset($_GET['matricule']) ? strval($_GET['matricule']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$matieres = $matiere->getAllMatiereParProf($matricule);

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
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Matieres</title>
    <style>
        :root {
            --gradient-light: linear-gradient(135deg, #74ebd5, #ACB6E5);
            --gradient-dark: linear-gradient(135deg, #1a1a1a, #2d3436);
            --bg-light: #ffffff;
            --bg-dark: #121212;
            --text-light: #000000;
            --text-dark: #ffffff;
            --border-light: #ddd;
            --border-dark: #333;
            --shadow-light: rgba(0, 0, 0, 0.2);
            --shadow-dark: rgba(0, 0, 0, 0.4);
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            min-height: 50vh;
            background: var(--gradient-light);
            color: var(--text-light);
            transition: all 0.3s ease;
        }

        body.dark-mode {
            background: var(--gradient-dark);
            color: var(--text-dark);
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--bg-light);
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 5px var(--shadow-light);
            transition: all 0.3s ease;
        }

        .dark-mode .theme-toggle {
            background: var(--bg-dark);
            color: var(--text-dark);
            box-shadow: 0 2px 5px var(--shadow-dark);
        }

        .sidebar {
            background: linear-gradient(var(--bg-light), #74ebd5, var(--bg-light));
            color: var(--text-light);
            width: 180px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 5px 5px 5px var(--shadow-light);
            border-radius: 10px;
            transition: all 0.3s ease;
            position: fixed;
            left: -200px; /* Caché par défaut */
            top: 0;
            z-index: 1000;
            height: 100vh; /* Prend toute la hauteur de l'écran */
        }

        .sidebar.active {
            left: 0; /* Afficher la sidebar */
        }

        .dark-mode .sidebar {
            background: linear-gradient(var(--bg-dark), #2d3436, var(--bg-dark));
            color: var(--text-dark);
            box-shadow: 5px 5px 5px var(--shadow-dark);
        }

        .sidebar h2 {
            margin: 0 0 20px 0;
            font-size: 1.5em;
            text-align: center;
        }

        .sidebar a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background: var(--gradient-light);
            transform: translateY(-2px);
        }

        .dark-mode .sidebar a:hover {
            background: var(--gradient-dark);
        }

        .sidebar .logout {
            margin-top: auto; /* Place la déconnexion en bas */
            margin-bottom: 20px; /* Ajoute un peu d'espace en bas */
        }

        .sidebar img {
            width: 20px; /* Taille réduite des images dans la sidebar */
            height: 20px;
            margin-right: 10px;
        }

        .content {
            margin-left: 170px;
            margin-right: -20px;
            padding: 20px;
            width: calc(100% - 170px);
            transition: all 0.3s ease;
        }

        .dark-mode .content {
            background: var(--bg-dark);
            color: var(--text-dark);
        }

        .header {
            background-color: var(--bg-light);
            padding: 15px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px;
        }

        .dark-mode .header {
            background-color: var(--bg-dark);
            border-bottom: 1px solid var(--border-dark);
        }

        .header h2 {
            margin-left: 10px;
            justify-content: left;
        }

        .header input {
            width: 300px;
            padding: 15px;
            border: 1px solid var(--border-light);
            border-radius: 5px;
        }

        .dark-mode .header input {
            border: 1px solid var(--border-dark);
        }

        .header input:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .header input::placeholder {
            color: #aaa;
        }

        .header button {
            background-color: var(--bg-light);
            color: var(--text-light);
            margin: 5px;
            padding: 9px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .dark-mode .header button {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        .header button:hover {
            background-color: wheat;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background-color: var(--bg-light);
            border-radius: 10px;
        }

        .dark-mode table {
            background-color: var(--bg-dark);
        }

        th,
        td {
            padding: 8px;
            border: 1px solid var(--border-light);
            text-align: left;
        }

        .dark-mode th,
        .dark-mode td {
            border: 1px solid var(--border-dark);
        }

        th {
            background-color: wheat;
            color: black;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .dark-mode tr:nth-child(even) {
            background-color: #2d3436;
        }

        tr:hover {
            background-color: #ddd;
        }

        .dark-mode tr:hover {
            background-color: #333;
        }

        .hamburger {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            cursor: pointer;
            background: var(--bg-light);
            padding: 10px;
            border-radius: 50%;
            box-shadow: 0 2px 5px var(--shadow-light);
            transition: all 0.3s ease;
        }

        .dark-mode .hamburger {
            background: var(--bg-dark);
            box-shadow: 0 2px 5px var(--shadow-dark);
        }

        .hamburger span {
            display: block;
            width: 25px;
            height: 3px;
            background: var(--text-light);
            margin: 5px 0;
            transition: all 0.3s ease;
        }

        .dark-mode .hamburger span {
            background: var(--text-dark);
        }

        .actions button img {
            width: 16px; /* Taille réduite des icônes dans les boutons */
            height: 16px;
        }
    </style>
</head>

<body>
    <button class="theme-toggle" onclick="toggleDarkMode()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="5"/>
            <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
        </svg>
    </button>

    <div class="hamburger" onclick="toggleSidebar()">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="sidebar">
        <h2>Menu</h2>
        <a href="infoProf.php?matricule=<?= htmlspecialchars($matricule); ?>"><img src="uploads/analytics.gif" alt="">Information</a>
        <a class="etu" href="MatiereProf.php?matricule=<?= htmlspecialchars($matricule); ?>"><img src="uploads/analytics.gif" alt="">Matieres</a>
        <a href="logout.php" class="logout"><img src="uploads/logout.gif" alt="">Déconnexion</a>
    </div>

    <div class="content">
        <div class="header">
            <div class="haut">
                <h2>Liste des Matieres</h2>
            </div>
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
                                <td><?= str_replace('_', ' ', htmlspecialchars($matiere['nom_matiere'])); ?></td>
                                <td><?= htmlspecialchars($matiere['niveau_matiere']); ?></td>
                                <td class="actions">
                                    <button class="edit" onclick="location.href='edit.php?id=<?= $matiere['id']; ?>'">
                                        <img src="uploads/write.png" alt="Modifier">
                                    </button>
                                    <button class="delete" onclick="openPopup(<?= $matiere['id']; ?>)">
                                        <img src="uploads/delete.png" alt="Supprimer">
                                    </button>
                                    <button class="details" onclick="location.href='AjouterNotes.php?niveau=<?= $matiere['niveau_matiere']; ?>&&matiere=<?= $matiere['nom_matiere']; ?>'">
                                        Ajouter Notes
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

    <script>
        // Gestion du mode sombre
        const darkMode = localStorage.getItem('darkMode');
        if (darkMode === 'enabled') {
            document.body.classList.add('dark-mode');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', null);
            }
        }

        // Gestion du menu hamburger
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }

        // Gestion de la popup de suppression
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