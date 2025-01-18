<?php
require_once 'Class/Database.php';
require_once 'Class/Etudiant.php';

$database = new Database();
$db = $database->getConnection();
$etudiant = new Etudiant($db);

// Recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'nom';
$etudiants = $etudiant->getAllEtudiants($search, $filter);

// Suppression
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($etudiant->supprimerEtudiant($id) && $etudiant->supprimerConnexion($id)) {
        $message = "L'étudiant a été supprimé avec succès.";
        header("Location: gestionEtu.php");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="bootstrap.css">
    <title>Liste des Étudiants</title>
    <style>
        /* Styles CSS fournis */
        .grid {
            height: 800px;
            width: 800px;
            background-image: linear-gradient(to right, #0f0f10 1px, transparent 1px),
                linear-gradient(to bottom, #0f0f10 1px, transparent 1px);
            background-size: 1rem 1rem;
            background-position: center center;
            position: absolute;
            z-index: -1;
            filter: blur(1px);
        }
        .white,
        .border,
        .darkBorderBg,
        .glow {
            max-height: 70px;
            max-width: 314px;
            height: 100%;
            width: 100%;
            position: absolute;
            overflow: hidden;
            z-index: -1;
            border-radius: 12px;
            filter: blur(3px);
        }
        .input {
            background-color: #010201;
            border: none;
            width: 301px;
            height: 56px;
            border-radius: 10px;
            color: white;
            padding-inline: 59px;
            font-size: 18px;
        }
        #poda {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .input::placeholder {
            color: #c0b9c0;
        }
        .input:focus {
            outline: none;
        }
        #main:focus-within > #input-mask {
            display: none;
        }
        #input-mask {
            pointer-events: none;
            width: 100px;
            height: 20px;
            position: absolute;
            background: linear-gradient(90deg, transparent, black);
            top: 18px;
            left: 70px;
        }
        #pink-mask {
            pointer-events: none;
            width: 30px;
            height: 20px;
            position: absolute;
            background: #cf30aa;
            top: 10px;
            left: 5px;
            filter: blur(20px);
            opacity: 0.8;
            transition: all 2s;
        }
        #main:hover > #pink-mask {
            opacity: 0;
        }
        .white {
            max-height: 63px;
            max-width: 307px;
            border-radius: 10px;
            filter: blur(2px);
        }
        .white::before {
            content: "";
            z-index: -2;
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(83deg);
            position: absolute;
            width: 600px;
            height: 600px;
            background-repeat: no-repeat;
            background-position: 0 0;
            filter: brightness(1.4);
            background-image: conic-gradient(
                rgba(0, 0, 0, 0) 0%,
                #a099d8,
                rgba(0, 0, 0, 0) 8%,
                rgba(0, 0, 0, 0) 50%,
                #dfa2da,
                rgba(0, 0, 0, 0) 58%
            );
            transition: all 2s;
        }
        .border {
            max-height: 59px;
            max-width: 303px;
            border-radius: 11px;
            filter: blur(0.5px);
        }
        .border::before {
            content: "";
            z-index: -2;
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(70deg);
            position: absolute;
            width: 600px;
            height: 600px;
            filter: brightness(1.3);
            background-repeat: no-repeat;
            background-position: 0 0;
            background-image: conic-gradient(
                #1c191c,
                #402fb5 5%,
                #1c191c 14%,
                #1c191c 50%,
                #cf30aa 60%,
                #1c191c 64%
            );
            transition: all 2s;
        }
        .darkBorderBg {
            max-height: 65px;
            max-width: 312px;
        }
        .darkBorderBg::before {
            content: "";
            z-index: -2;
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(82deg);
            position: absolute;
            width: 600px;
            height: 600px;
            background-repeat: no-repeat;
            background-position: 0 0;
            background-image: conic-gradient(
                rgba(0, 0, 0, 0),
                #18116a,
                rgba(0, 0, 0, 0) 10%,
                rgba(0, 0, 0, 0) 50%,
                #6e1b60,
                rgba(0, 0, 0, 0) 60%
            );
            transition: all 2s;
        }
        #poda:hover > .darkBorderBg::before {
            transform: translate(-50%, -50%) rotate(262deg);
        }
        #poda:hover > .glow::before {
            transform: translate(-50%, -50%) rotate(240deg);
        }
        #poda:hover > .white::before {
            transform: translate(-50%, -50%) rotate(263deg);
        }
        #poda:hover > .border::before {
            transform: translate(-50%, -50%) rotate(250deg);
        }
        #poda:focus-within > .darkBorderBg::before {
            transform: translate(-50%, -50%) rotate(442deg);
            transition: all 4s;
        }
        #poda:focus-within > .glow::before {
            transform: translate(-50%, -50%) rotate(420deg);
            transition: all 4s;
        }
        #poda:focus-within > .white::before {
            transform: translate(-50%, -50%) rotate(443deg);
            transition: all 4s;
        }
        #poda:focus-within > .border::before {
            transform: translate(-50%, -50%) rotate(430deg);
            transition: all 4s;
        }
        .glow {
            overflow: hidden;
            filter: blur(30px);
            opacity: 0.4;
            max-height: 130px;
            max-width: 354px;
        }
        .glow:before {
            content: "";
            z-index: -2;
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(60deg);
            position: absolute;
            width: 999px;
            height: 999px;
            background-repeat: no-repeat;
            background-position: 0 0;
            background-image: conic-gradient(
                #000,
                #402fb5 5%,
                #000 38%,
                #000 50%,
                #cf30aa 60%,
                #000 87%
            );
            transition: all 2s;
        }
        @keyframes rotate {
            100% {
                transform: translate(-50%, -50%) rotate(450deg);
            }
        }
        @keyframes leftright {
            0% {
                transform: translate(0px, 0px);
                opacity: 1;
            }
            49% {
                transform: translate(250px, 0px);
                opacity: 0;
            }
            80% {
                transform: translate(-40px, 0px);
                opacity: 0;
            }
            100% {
                transform: translate(0px, 0px);
                opacity: 1;
            }
        }
        #filter-icon {
            position: absolute;
            top: 8px;
            right: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            max-height: 40px;
            max-width: 38px;
            height: 100%;
            width: 100%;
            isolation: isolate;
            overflow: hidden;
            border-radius: 10px;
            background: linear-gradient(180deg, #161329, black, #1d1b4b);
            border: 1px solid transparent;
        }
        .filterBorder {
            height: 42px;
            width: 40px;
            position: absolute;
            overflow: hidden;
            top: 7px;
            right: 7px;
            border-radius: 10px;
        }
        .filterBorder::before {
            content: "";
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(90deg);
            position: absolute;
            width: 600px;
            height: 600px;
            background-repeat: no-repeat;
            background-position: 0 0;
            filter: brightness(1.35);
            background-image: conic-gradient(
                rgba(0, 0, 0, 0),
                #3d3a4f,
                rgba(0, 0, 0, 0) 50%,
                rgba(0, 0, 0, 0) 50%,
                #3d3a4f,
                rgba(0, 0, 0, 0) 100%
            );
            animation: rotate 4s linear infinite;
        }
        #main {
            position: relative;
        }
        #search-icon {
            position: absolute;
            left: 20px;
            top: 15px;
        }

        /* Styles existants */
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
            z-index: 1000;
        }
        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
            max-width: 400px;
            width: 90%;
            position: relative;
        }
        .popup-content h3 {
            margin: 0 0 10px;
        }
        .popup-content p {
            margin: 0 0 20px;
        }
        .popup-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 5px;
        }
        .popup-actions .close {
            background: #2196F3;
            color: #fff;
        }
        .popup-actions .close:hover {
            background: #1976D2;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: rgb(1, 1, 2);
        }
        .navbar {
            background-color: rgb(44, 25, 3);
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            text-decoration: none;
            color: white;
            margin: 0 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .navbar a:hover {
            background-color: rgb(209, 142, 115);
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(245, 165, 90, 0.5);
        }
        .navbar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        .navbar .etu {
            background-color: rgb(87, 70, 64);
        }
        .content {
            margin: 20px;
            padding: 20px;
        }
        .header {
            background-color: #fff;
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px;
        }
        .header h2:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }
        .header h2 {
            margin-left: 10px;
            justify-content: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background-color: #fff;
            border-radius: 10px;
        }
        .table-container {
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table-container a {
            text-decoration: none;
        }
        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: wheat;
            color: black;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        h1 {
            text-align: center;
            color: #2196F3;
        }
        img {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 50px;
            width: 100%;
        }
        .rien {
            max-width: 25px;
            max-height: 25px;
            justify-content: center;
        }
        .actions button {
            margin: .2px;
            padding: 3px 6px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .actions button:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }
        .actions .edit {
            background-color: whitesmoke;
            color: white;
        }
        .actions .details {
            background-color: whitesmoke;
            color: white;
        }
        .actions .delete {
            background-color: whitesmoke;
            color: white;
        }
        .ajout {
            background-color: rgb(32, 79, 117);
            color: white;
            display: flex;
            align-items: center;
            margin: 5px 0px;
            padding: 12px 15px;
            border: none;
            border-radius: 22px;
            cursor: pointer;
            justify-content: center;
            position: relative;
            text-decoration: none;
        }
        .ajout:hover {
            background-color: #475be8;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }
        .table-container .hover .ajout:hover {
            background-color: #475be8;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }
        .haut {
            padding: 10px;
        }
        .haut h2 {
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        /* Nouveau style pour le bouton de filtre */
        .filter-btn {
            background-color: #104d7d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
        }
        .filter-btn:hover {
            background-color: #475be8;
        }
        .filter-btn i {
            font-size: 18px;
        }

        /* Style pour le menu déroulant */
        .filter-dropdown {
            position: relative;
            display: inline-block;
        }
        .filter-dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
        }
        .filter-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s ease;
        }
        .filter-dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .filter-dropdown:hover .filter-dropdown-content {
            display: block;
        }
    </style>
</head>

<body>
    <?php if (!empty($message)): ?>
        <div class="popup-overlay" id="popup">
            <div class="popup-content">
                <h3>Message</h3>
                <p><?= htmlspecialchars($message); ?></p>
                <div class="popup-actions">
                    <button class="close" onclick="closePopup()">Fermer</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Navbar -->
    <div class="navbar">
        <a class="etu" href="gestionEtu.php"> <i class="fas fa-user-graduate"></i> Étudiants </a>
        <a href="gestionVer.php"><i class="fas fa-money-bill-wave"></i> Versements</a>
        <a href="gestionEnsei.php"><i class="fas fa-chalkboard-teacher"></i> Enseignant</a>
        <a href="statistiques.php"> <i class="fas fa-chart-bar"></i> Statistiques</a>
        <a href="gestionMati.php"> <i class="fas fa-book"></i>  Matieres</a>
        <a href="listeNote.php"> <i class="fas fa-graduation-cap"></i> Notes</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="header">
            <div class="haut">
                <h2><i class="fa fa-users"></i> Liste des étudiants</h2>
            </div>
            <!-- Nouveau formulaire de recherche avec bouton de filtre stylisé -->
            <div class="grid"></div>
            <div id="poda">
                <div class="glow"></div>
                <div class="darkBorderBg"></div>
                <div class="darkBorderBg"></div>
                <div class="darkBorderBg"></div>
                <div class="white"></div>
                <div class="border"></div>
                <div id="main">
                    <form method="GET" action="gestionEtu.php">
                        <input name="search" type="text" placeholder="Search..." class="input" value="<?= htmlspecialchars($search); ?>">
                        <div class="filter-dropdown">
                            <button type="button" class="filter-btn">
                                <i class="fas fa-filter"></i> Filtre
                            </button>
                            <div class="filter-dropdown-content">
                                <a href="?filter=nom&search=<?= htmlspecialchars($search); ?>">Nom</a>
                                <a href="?filter=niveau&search=<?= htmlspecialchars($search); ?>">Niveau</a>
                                <a href="?filter=matricule&search=<?= htmlspecialchars($search); ?>">Matricule</a>
                            </div>
                        </div>
                        <div id="input-mask"></div>
                        <div id="pink-mask"></div>
                        <div class="filterBorder"></div>
                        <div id="search-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" height="24" fill="none" class="feather feather-search">
                                <circle stroke="url(#search)" r="8" cy="11" cx="11"></circle>
                                <line stroke="url(#searchl)" y2="16.65" y1="22" x2="16.65" x1="22"></line>
                                <defs>
                                    <linearGradient gradientTransform="rotate(50)" id="search">
                                        <stop stop-color="#f8e7f8" offset="0%"></stop>
                                        <stop stop-color="#b6a9b7" offset="50%"></stop>
                                    </linearGradient>
                                    <linearGradient id="searchl">
                                        <stop stop-color="#b6a9b7" offset="0%"></stop>
                                        <stop stop-color="#837484" offset="50%"></stop>
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <p style="color: green;"> <?= htmlspecialchars($message); ?> </p>
        <?php endif; ?>

        <div class="table-container">
            <a href="AjouterEtu.php"><button class="ajout">Ajouter un étudiant</button></a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Matricule</th>
                        <th>Photo</th>
                        <th>Email</th>
                        <th>Niveau</th>
                        <th>Âge</th>
                        <th>Montant à Payer</th>
                        <th>Nom Parent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($etudiants)): ?>
                        <?php foreach ($etudiants as $etudiant): ?>
                            <tr>
                                <td><?= htmlspecialchars($etudiant['id']); ?></td>
                                <td><?= htmlspecialchars($etudiant['nom']); ?></td>
                                <td><?= htmlspecialchars($etudiant['prenom']); ?></td>
                                <td><?= htmlspecialchars($etudiant['matricule']); ?></td>
                                <td>
                                    <?php if (!empty($etudiant['photo'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($etudiant['photo']); ?>" alt="Photo">
                                    <?php else: ?>
                                        <span>Aucune photo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($etudiant['email']); ?></td>
                                <td><?= htmlspecialchars($etudiant['niveau']); ?></td>
                                <td><?= htmlspecialchars($etudiant['age']); ?></td>
                                <td><?= htmlspecialchars($etudiant['montant']); ?></td>
                                <td><?= htmlspecialchars($etudiant['nom_parent']); ?></td>
                                <td class="actions">
                                    <button class="edit" onclick="location.href='edit.php?id=<?= $etudiant['id']; ?>'"><img src="uploads/stylo.png" alt=""></button>
                                    <button class="delete" onclick="openPopup(<?= $etudiant['id']; ?>)"><img src="uploads/delete.png" alt=""></button>
                                    <button class="details" onclick="location.href='informationEtudiant.php?id=<?= $etudiant['id']; ?>'"><img src="uploads/info.png" alt=""></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12">Aucun étudiant trouvé.</td>
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