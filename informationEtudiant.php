<?php
require_once 'Class/Database.php';
require_once 'Class/Etudiant.php';
require 'fpdf.php'; // Inclure la bibliothèque FPDF

// Fonction pour convertir une image PNG en 8 bits
function convertTo8Bit($source, $destination)
{
    $image = imagecreatefrompng($source);
    if ($image === false) {
        die("Impossible de charger l'image.");
    }

    $width = imagesx($image);
    $height = imagesy($image);

    $newImage = imagecreatetruecolor($width, $height);
    imagecopy($newImage, $image, 0, 0, 0, 0, $width, $height);

    // Convertir en 8 bits
    imagetruecolortopalette($newImage, false, 256);
    imagepng($newImage, $destination);

    imagedestroy($image);
    imagedestroy($newImage);
}

// Vérifier si un ID est passé
if (!isset($_GET['matricule'])) {
    die("Aucun étudiant sélectionné.");
}

// Initialisation de la base de données et récupération des détails
$database = new Database();
$db = $database->getConnection();
$etudiantObj = new Etudiant($db);

$matricule = strval($_GET['matricule']);
$etudiant = $etudiantObj->obtenirEtudiantParId($matricule);

if (!$etudiant) {
    die("Étudiant introuvable.");
}

// Gestion de l'action pour télécharger le PDF
if (isset($_GET['action']) && $_GET['action'] === 'download_pdf') {
    // Préparer l'image pour FPDF
    $originalImage = 'uploads/' . $etudiant['photo'];
    $convertedImage = 'uploads/' . pathinfo($etudiant['photo'], PATHINFO_FILENAME) . '_converted.png';

    // Convertir l'image en 8 bits
    convertTo8Bit($originalImage, $convertedImage);

    // Générer le PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Ajouter un en-tête
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(50, 50, 50);
    $pdf->Cell(0, 10, "Carte d'Etudiant", 0, 1, 'C');
    $pdf->Ln(10);

    // Ajouter un logo (optionnel, si vous avez un fichier logo.png dans le dossier)
    $logoPath = 'logo.png';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 10, 10, 30, 30);
    }

    // Ajouter une bordure de carte
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Rect(10, 50, 190, 120);

    // Ajouter la photo de l'étudiant
    $pdf->Image($convertedImage, 20, 60, 40, 40);

    // Ajouter les détails de l'étudiant
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetXY(70, 60);
    $pdf->Cell(60, 10, 'Nom: ' . $etudiant['nom'], 0, 1);
    $pdf->SetX(70);
    $pdf->Cell(60, 10, 'Prenom: ' . $etudiant['prenom'], 0, 1);
    $pdf->SetX(70);
    $pdf->Cell(60, 10, 'Matricule: ' . $etudiant['matricule'], 0, 1);
    $pdf->SetX(70);
    $pdf->Cell(60, 10, 'Email: ' . $etudiant['email'], 0, 1);

    $pdf->Ln(10);
    $pdf->SetX(70);
    $pdf->Cell(60, 10, 'Niveau: ' . $etudiant['niveau'], 0, 1);
    $pdf->SetX(70);
    $pdf->Cell(60, 10, 'Age: ' . $etudiant['age'], 0, 1);

    // Ajouter un pied de page
    $pdf->SetXY(10, 180);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 10, 'Genere le ' . date('d/m/Y') . ' - Keyce informatique', 0, 1, 'C');

    // Envoyer le PDF au navigateur pour téléchargement
    $pdf->Output('D', 'Carte_Etudiant_' . $matricule . '.pdf');

    // Supprimer l'image temporaire
    unlink($convertedImage);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Carte de l'étudiant</title>
    <title>Détails de l'Étudiant</title>
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

        .container {
            max-width: 700px;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .student-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
        }

        .student-photo:hover {
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
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

        .back-link,
        .pdf-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 25px;
            transition: background 0.3s;
        }

        .back-link {
            background-color: wheat;
            color: black;
            border: 2px solid wheat;
        }

        .back-link:hover {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: white;
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .pdf-link {
            background-color: #4caf50;
            color: white;
        }

        .pdf-link:hover {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            transform: translateY(-10%);
            transition: ease-in-out .5s;
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
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

        .sidebar a img {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .sidebar a:hover {
            background: linear-gradient(135deg, #74ebd5, rgb(141, 77, 17));
            transform: translateY(-2px);
        }

        .sidebar .etu {
            background: linear-gradient(135deg, #eb7974, #ACB6E5);
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
        }

        .overlay.visible {
            opacity: 1;
            visibility: visible;
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

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Menu</h2>
        <a class="etu" href="informationEtudiant.php?matricule=<?= $etudiant['matricule']; ?>" $matricule1>Informations</a>
        <a href="MatiereEtu.php?niveau=<?= $etudiant['niveau']; ?>&&matricule=<?= $etudiant['matricule']; ?>">Matieres</a>
        <a href="NoteEtudiant.php?niveau=<?= $etudiant['niveau']; ?>&&matricule=<?= $etudiant['matricule']; ?>">Notes</a>
        <a class="tr" href="index.php"><i class="fas fa-sign-out-alt"></i>Déconnexion</a>
    </div>

    <!-- Overlay -->
    <div class="overlay"></div>

    <div class="container">
        <h1>Détails de l'Étudiant</h1>
        <img src="uploads/<?php echo htmlspecialchars($etudiant['photo']); ?>" alt="Photo de l'étudiant" class="student-photo">
        <table>
            <tr>
                <th>Nom</th>
                <td><?php echo htmlspecialchars($etudiant['nom']); ?></td>
            </tr>
            <tr>
                <th>Prénom</th>
                <td><?php echo htmlspecialchars($etudiant['prenom']); ?></td>
            </tr>
            <tr>
                <th>Matricule</th>
                <td><?php echo htmlspecialchars($etudiant['matricule']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($etudiant['email']); ?></td>
            </tr>
            <tr>
                <th>Niveau</th>
                <td><?php echo htmlspecialchars($etudiant['niveau']); ?></td>
            </tr>
            <tr>
                <th>Âge</th>
                <td><?php echo htmlspecialchars($etudiant['age']); ?></td>
            </tr>
            <tr>
                <th>Montant à Payer</th>
                <td><?php echo htmlspecialchars($etudiant['montant']); ?> FCFA</td>
            </tr>
            <tr>
                <th>Nom Parent</th>
                <td><?php echo htmlspecialchars($etudiant['nom_parent']); ?></td>
            </tr>
            <tr>
                <th>Email Parent</th>
                <td><?php echo htmlspecialchars($etudiant['email_parent']); ?></td>
            </tr>
        </table>
        <a href="?matricule=<?= $matricule; ?>&action=download_pdf" class="pdf-link">Télécharger la carte</a>
    </div>

    <script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('visible');
    }
</script>
