<?php
require_once 'Class/Database.php';
require_once 'Class/Enseignant.php';
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
$etudiantObj = new Enseignant($db);

$matricule = strval($_GET['matricule']);
$enseignant = $etudiantObj->obtenirEtudiantParMatricule($matricule);

if (!$enseignant) {
    die("Étudiant introuvable.");
}

// Gestion de l'action pour télécharger le PDF
if (isset($_GET['action']) && $_GET['action'] === 'download_pdf') {
    // Préparer l'image pour FPDF
    $originalImage = $enseignant['photo'];
    $convertedImage =  pathinfo($enseignant['photo'], PATHINFO_FILENAME) . '_converted.png';

    // Convertir l'image en 8 bits
    convertTo8Bit($originalImage, $convertedImage);

    // Générer le PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Ajouter un en-tête
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(50, 50, 50);
    $pdf->Cell(0, 10, "Carte du Professeur", 0, 1, 'C');
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
    $pdf->Cell(60, 10, 'Nom: ' . $enseignant['nom'], 0, 1);
    $pdf->SetX(70);
    $pdf->Cell(60, 10, 'Prenom: ' . $enseignant['prenom'], 0, 1);
    $pdf->SetX(70);
    $pdf->Cell(60, 10, 'Matricule: ' . $enseignant['matricule'], 0, 1);
    $pdf->SetX(70);
    $pdf->Cell(60, 10, 'Email: ' . $enseignant['email'], 0, 1);

    $pdf->Ln(10);
    $pdf->SetX(70);
    $pdf->Cell(60, 10, 'Fonction: ' . $enseignant['fonction'], 0, 1);
    

    // Ajouter un pied de page
    $pdf->SetXY(10, 180);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 10, 'Genere le ' . date('d/m/Y') . ' - Keyce informatique', 0, 1, 'C');

    // Envoyer le PDF au navigateur pour téléchargement
    $pdf->Output('D', 'Carte_Professeur_' . $matricule . '.pdf');

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
    <title>Détails de l'Étudiant</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 50vh;
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
        .student-photo:hover{
            transform:translateY(-10%);
            transition: ease-in-out .5s ;
            box-shadow: 0 5px 15px rgba(102,166,255,0.5);
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
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: white;
        }

        .back-link,
        .pdf-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .back-link {
            background-color: wheat;
            color: black;
            border: 2px;
        }

        .back-link:hover {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: white;
            transform:translateY(-10%);
            transition: ease-in-out .5s ;
            box-shadow: 0 5px 15px rgba(102,166,255,0.5);
        }

        .pdf-link {
            background-color: #4caf50;
            color: white;
        }

        .pdf-link:hover {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            transform:translateY(-10%);
            transition: ease-in-out .5s ;
            box-shadow: 0 5px 15px rgba(102,166,255,0.5);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Détails du Professeur</h1>
        <img src="<?php echo htmlspecialchars($enseignant['photo']); ?>" alt="Photo de l'étudiant" class="student-photo">
        <table>
            <tr>
                <th>Nom</th>
                <td><?php echo htmlspecialchars($enseignant['nom']); ?></td>
            </tr>
            <tr>
                <th>Prénom</th>
                <td><?php echo htmlspecialchars($enseignant['prenom']); ?></td>
            </tr>
            <tr>
                <th>Matricule</th>
                <td><?php echo htmlspecialchars($enseignant['matricule']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($enseignant['email']); ?></td>
            </tr>
            <tr>
                <th>Fonction</th>
                <td><?php echo htmlspecialchars($enseignant['fonction']); ?></td>
            </tr>
           
            
        </table>
        <a href="?matricule=<?= $enseignant['matricule']; ?>&action=download_pdf" class="pdf-link">Télécharger la carte</a>
        <a href="gestionVer.php" class="back-link">Retour </a>
    </div>
</body>

</html>