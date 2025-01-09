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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Matieres</title>
    <style>
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
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(#fff, #74ebd5, #fff);
            color: black;
            height: 93vh;
            position: fixed;
            width: 145px;
            padding: 20px;
            border-radius: 10px;
        }

        .sidebar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .sidebar h2 {
            /* text-align: left;
            margin-bottom: 10px; Réduit la marge */
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a {
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
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

        /* Content */
        .content {
            margin-left: 170px;
            margin-right: -20px;
            padding: 20px;
            padding: 20px;
            /* Ajuste la largeur pour s'adapter à la sidebar */
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

        .header input {
            width: 300px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
            background-color: whitesmoke;
            color: black;
            margin: 5px;
            padding: 9px 12px;
            /* Réduit le padding */
            border: none;
            border-radius: 5px;
            cursor: pointer;
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
            /* Réduit la marge */
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
            /* Réduit le padding */
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
            /* Réduit la taille de l'image */
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
            /* Réduit la marge */
            padding: 3px 6px;
            /* Réduit le padding */
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
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: black;
            margin: 10px;
            padding: 10px;
        }

        .actions .delete {
            background-color: whitesmoke;
            color: white;
        }

        .ajout {
            background-color: #2196F3;
            color: white;
            display: flex;
            /* display: flex; */
            align-items: center;
            margin: 5px 0px;
            padding: 12px 15px;
            /* Réduit le padding */
            border: none;
            border-radius: 5px;
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

        .popup-actions .cancel {
            background: #f44336;
            color: #fff;
        }

        .popup-actions .confirm {
            background: #4CAF50;
            color: #fff;
        }

        .popup-actions .cancel:hover {
            background: #d32f2f;
        }

        .popup-actions .confirm:hover {
            background: #388E3C;
        }

        .haut {
            /* justify-content: left; */
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

        .sidebar .etu {
            background-color: #6c83f7;
        }

        .logout {
            background-color: #f44336;
            /* Couleur rouge pour le bouton de déconnexion */
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 28px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .logout:hover {
            background-color: #d32f2f;
            /* Couleur plus foncée au survol */
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
    <!-- Sidebar -->
    <!-- <div class="sidebar">
        <h2>Menu</h2>
        <?php if (!empty($matieres)): ?>
        <a href="infoProf.php?matricule=<?= htmlspecialchars($matricule); ?>"><img src="uploads/analytics.gif" alt="">Information</a>
        <?php endif; ?>
        <?php if (!empty($matieres)): ?>
        <a class="etu" href="MatiereProf.php?matricule=<?= htmlspecialchars($matricule); ?>"><img src="uploads/analytics.gif" alt="">Matieres</a>
        <?php endif; ?>
        <a href="logout.php">Déconnexion</a>
    </div> -->

    <!-- Main Content -->
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
                                        Ajouter Notes<!-- <img src="uploads/info.png" alt="Détails"> -->
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
    <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>

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

    <script>
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }
    </script>
</body>

</html>