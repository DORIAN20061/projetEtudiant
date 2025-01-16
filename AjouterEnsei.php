<?php
require('fpdf.php');
require('Class/Etudiant.php');
require_once('Class/Database.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Class/vendor/autoload.php';
$db1 = new Database();   
$db = $db1->getConnection();
$message = '';
$etudiant = new Etudiant($db);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $fonction = $_POST['fonction'];
    $photo = $_FILES['photo'];

    // Génération du matricule et du compte
    $matricule = "KIA-" . date("Ymd");
    $password = $matricule;


    // Enregistrement de la photo
    $photoPath = 'uploads/' . $matricule . '_' . basename($photo['name']);
    move_uploaded_file($photo['tmp_name'], $photoPath);

    // Connexion à la base de données
    $mysqli = new mysqli('127.0.0.1', 'root', '', 'etudiants');
    if ($mysqli->connect_error) {
        die("Erreur de connexion : " . $mysqli->connect_error);
    }

    // Insertion dans la table
    $stmt = $mysqli->prepare("INSERT INTO enseignants (matricule, nom, prenom, photo, email, fonction) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $matricule, $nom, $prenom, $photoPath, $email, $fonction);
    $stmt1 = $mysqli->prepare("INSERT INTO connexion_prof (matricule, password) VALUES (?, ?)");
    $stmt1->bind_param("ss", $matricule, $matricule);
    if ($stmt->execute() && $stmt1->execute()) {
        $etudiant->ajouterEtudiantCon("Professeur", $matricule, $matricule);
        // Générer la carte en PDF
        $message2 = generateTeacherCard($nom, $prenom, $matricule, $fonction, $photoPath, $email);
        $message = "Enseignant ajouté avec succès. ".$message2;
       
    } else {
        echo "Erreur lors de l'ajout : " . $mysqli->error;
    }

    $stmt->close();
    $mysqli->close();
}

// Fonction pour générer la carte en PDF
function generateTeacherCard($nom, $prenom, $matricule, $fonction, $photoPath, $email)
{


    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Titre
    $pdf->Cell(0, 10, 'Carte d\'enseignant', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);

    // Nom de l'établissement
    $pdf->Cell(0, 10, 'Keyce Informatique & Intelligence Artificielle', 0, 1, 'C');
    $pdf->Image('logo.jpeg', 10, 10, 30); 

    // Informations
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Nom & Prenom : $nom $prenom", 0, 1);
    $pdf->Cell(0, 10, "Matricule : $matricule", 0, 1);
    $pdf->Cell(0, 10, "Fonction : $fonction", 0, 1);
    $pdf->Cell(0, 10, "Date d'enregistrement : " . date("Y-m-d"), 0, 1);

    // Photo
    if (file_exists($photoPath)) {
        $pdf->Image($photoPath, 150, 20, 40);
    }

    // Sauvegarde du fichier PDF
    $fileName = "cartes/$matricule.pdf";
    $pdf->Output('F', $fileName);

    // Envoi par email
    $message1 =sendEmailWithAttachment($email, $fileName);
    return $message1;
}

// Fonction pour envoyer un email avec PHPMailer
function sendEmailWithAttachment($email, $filePath)
{
    // require 'PHPMailer/PHPMailerAutoload.php';
    
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Remplacez par le serveur SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'mininoulilou@gmail.com';
    $mail->Password = 'hpqz zoke gzcq bkfb';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('mininoulilou@gmail.com', 'Keyce Informatique');
    $mail->addAddress($email);
    $mail->Subject = 'Votre carte d\'enseignant';
    $mail->Body = 'Veuillez trouver ci-joint votre carte d\'enseignant.';
    $mail->addAttachment($filePath);

    if (!$mail->send()) {
        echo 'Erreur lors de l\'envoi de l\'email : ' . $mail->ErrorInfo;
    } else {
        return 'Carte envoyée par email avec succès.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un enseignant</title>
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
        }

        .popup-content h3 {
            margin: 0 0 10px;
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
            background: #1769aa;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg,rgb(11, 16, 19),rgb(66, 30, 7));
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        h1 {
            text-align: center;
            color: #444;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #5cb85c;
            color: #fff;
            padding: 10px 15px;
            margin: 36px;
            border: none;
            border-radius: 68px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Ajouter un enseignant</h1>
        <form action="AjouterEnsei.php" method="POST" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="fonction">Fonction :</label>
            <input type="text" id="fonction" name="fonction" required>

            <label for="photo">Photo :</label>
            <input type="file" id="photo" name="photo" accept="image/*" required>

            <button type="submit">Ajouter l'enseignant</button>
        </form>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div id="popup" class="popup-overlay">
            <div class="popup-content">
                <h3><?= htmlspecialchars($message); ?></h3>
                <div class="popup-actions">
                    <button class="close" onclick="closePopup()">Fermer</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function closePopup() {
            const popup = document.getElementById('popup');
            popup.style.display = 'none';
        }
    </script>
</body>

</html>