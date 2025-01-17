<?php
require_once 'Class/Database.php';
require_once 'Class/Enseignant.php';
require 'fpdf.php'; // Inclure la bibliothèque FPDF

// Fonction pour convertir une image en 8 bits (supporte PNG et JPEG)
function convertTo8Bit($source, $destination) {
    // Vérifier le type de fichier
    $imageInfo = getimagesize($source);
    if ($imageInfo === false) {
        die("Impossible de déterminer le type de fichier.");
    }

    $mimeType = $imageInfo['mime'];

    // Charger l'image en fonction de son type
    switch ($mimeType) {
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/jpeg':
        case 'image/jpg':
            $image = imagecreatefromjpeg($source);
            break;
        default:
            die("Format d'image non supporté.");
    }

    if ($image === false) {
        die("Impossible de charger l'image.");
    }

    $width = imagesx($image);
    $height = imagesy($image);

    $newImage = imagecreatetruecolor($width, $height);
    imagecopy($newImage, $image, 0, 0, 0, 0, $width, $height);

    // Convertir en 8 bits
    imagetruecolortopalette($newImage, false, 256);

    // Enregistrer l'image convertie
    imagepng($newImage, $destination);

    // Libérer la mémoire
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

$matricule1 = htmlspecialchars($enseignant['matricule']);

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
    $logoPath = 'keyce.jpg';
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
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Étudiant</title>
    <!-- Ajouter Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --gradient-light: linear-gradient(135deg, rgb(183, 194, 192), #ACB6E5);
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
            background: linear-gradient(var(--bg-light), rgb(145, 152, 151), var(--bg-light));
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
            border-radius: 20px;
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
            margin-bottom: 17px; /* Ajoute un peu d'espace en bas */
        }

        .container {
            max-width: 700px;
            background: var(--bg-light);
            padding: 86px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px var(--shadow-light);
            text-align: center;
            margin-left: 675px;
            transition: all 0.3s ease;
        }

        .dark-mode .container {
            background: var(--bg-dark);
            box-shadow: 0px 5px 15px var(--shadow-dark);
        }

        .student-photo {
            width: 150px;
            height: 150px;
            border-radius: 10%;
            object-fit: cover;
            margin: 0 auto 20px;
            transition: all 0.3s ease;
        }

        .student-photo:hover {
            transform: translateY(-10%);
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid var(--border-light);
            text-align: left;
        }

        .dark-mode th, .dark-mode td {
            border-bottom: 1px solid var(--border-dark);
        }

        th {
            background: var(--gradient-light);
            color: var(--text-light);
        }

        .dark-mode th {
            background: var(--gradient-dark);
            color: var(--text-dark);
        }

        .back-link, .pdf-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .back-link {
            background-color: wheat;
            color: var(--text-light);
        }

        .dark-mode .back-link {
            background-color: #2d3436;
            color: var(--text-dark);
        }

        .pdf-link {
            background-color: #4caf50;
            color: white;
        }

        .dark-mode .pdf-link {
            background-color: #2d5a30;
        }

        .back-link:hover, .pdf-link:hover {
            background: var(--gradient-light);
            transform: translateY(-10%);
            box-shadow: 0 5px 15px rgba(102, 166, 255, 0.5);
        }

        .dark-mode .back-link:hover, .dark-mode .pdf-link:hover {
            background: var(--gradient-dark);
        }

        .sidebar img {
            width: 33px;
            height: 38px;
            margin-right: 18px;
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
        <a class="etu" href="infoProf.php?matricule=<?= $enseignant['matricule']; ?>">
            <i class="fas fa-info-circle"></i> Information
        </a>
        <a href="MatiereProf.php?matricule=<?= $enseignant['matricule']; ?>">
            <i class="fas fa-book"></i> Matières
        </a>
        <a href="logout.php" class="logout">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>

    <div class="container">
        <h1>Détails du Professeur</h1>
        <img src="<?php echo htmlspecialchars($enseignant['photo']); ?>" alt="Photo de l'enseignant" class="student-photo">
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
        <!-- <a href="gestionVer.php" class="back-link">Retour</a> -->
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
    </script>
</body>
</html>