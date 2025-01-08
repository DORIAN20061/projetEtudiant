<?php
require_once 'Class/Database.php';
require 'fpdf.php';
require 'Class/vendor/phpmailer/phpmailer/src/Exception.php';
require 'Class/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'Class/vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new Database();
$db = $database->getConnection(); // Assurez-vous que cette méthode retourne une connexion MySQLi

// Récupérer la classe sélectionnée
$classe = $_GET['classe'];
$table_notes = strtolower($classe);

// Récupérer les notes des étudiants de la classe sélectionnée
$query = "SELECT * FROM $table_notes";
$result = $db->query($query);
if (!$result) {
    die("Erreur de requête : " . $db->error);
}
$notes = $result->fetch_all(MYSQLI_ASSOC);

foreach ($notes as $student) {
    $matricule = $student['matricule'];
    $nom = $student['nom'];
    $prenom = $student['prenom'];

    // Récupérer les informations de l'étudiant
    $query = "SELECT email, email_parent, nom_parent FROM etudiants WHERE matricule = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $matricule);
    $stmt->execute();
    $result_etudiant = $stmt->get_result();
    $etudiant = $result_etudiant->fetch_assoc();

    if ($etudiant) {
        $email_parent = $etudiant['email_parent'];
        $email_eleve = $etudiant['email'];
        $nom_parent = $etudiant['nom_parent'];

        // Calculer la moyenne générale
        $moyenne_generale = calculateAverage($student);

        // Générer le PDF du relevé de notes
        $pdf_path = generateReport($matricule, $nom, $prenom, $student, $moyenne_generale, 1); // Remplacez 1 par le rang réel

        // Générer le texte d'orientation via Gemini
        $orientation_text = gemini($nom, $prenom, $student);

        // Envoyer l'email avec le PDF et le texte d'orientation
        //sendEmail($email_parent, $email_eleve, $nom, $prenom, $pdf_path, $orientation_text);
    }
}

header('Location: listeNote.php');
exit();

// Fonction pour calculer la moyenne générale
function calculateAverage($notes) {
    $sum = 0;
    $count = 0;
    foreach ($notes as $key => $value) {
        if (!in_array($key, ['id', 'matricule', 'nom', 'prenom'])) {
            $sum += $value;
            $count++;
        }
    }
    return $count > 0 ? $sum / $count : 0;
}

// Fonction pour générer le PDF du relevé de notes
function generateReport($matricule, $nom, $prenom, $notes, $moyenne_generale, $rang) {
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);

    // Ajout du logo
    $logo_path = 'logo.jpeg'; // Chemin du logo
    if (file_exists($logo_path)) {
        $pdf->Image($logo_path, 10, 10, 30, 30); // Logo en haut à gauche
    }

    // Contour de la page
    $pdf->SetLineWidth(0.5);
    $pdf->Rect(10, 10, 190, 270, 'D'); // Bordure autour du contenu

    // Titre principal
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetXY(50, 20); // Position centrée
    $pdf->Cell(110, 10, 'Releve de Notes', 0, 1, 'C');

    $pdf->Ln(20);

    // Informations de l'étudiant
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 10, "Matricule : $matricule", 0, 1, 'C');
    $pdf->Cell(0, 10, "Nom et Prenom : $nom $prenom", 0, 1, 'C');
    $pdf->Cell(0, 10, "Moyenne Générale : $moyenne_generale", 0, 1, 'C');
    $pdf->Cell(0, 10, "Rang : $rang", 0, 1, 'C');

    $pdf->Ln(20);

    // Notes par matière
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Notes par Matiere", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($notes as $key => $value) {
        if (!in_array($key, ['id', 'matricule', 'nom', 'prenom'])) {
            $pdf->Cell(0, 10, ucfirst($key) . ": $value", 0, 1, 'L');
        }
    }

    $pdf->Ln(20);

    // Texte informatif (footer)
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 10, 'Produit par Keyce Informatique et Intelligence Artificielle', 0, 1, 'C');

    // Sortie PDF
    $pdf_path = "releves/releve_$matricule.pdf";
    $pdf->Output('F', $pdf_path);

    return $pdf_path;
}

// Fonction pour générer un texte d'orientation via l'API Gemini
function gemini($nom, $prenom, $notes) {
    // Clé API
    $GKey = "AIzaSyCppIR7-I0eqmDgYYXv_EqONSxD3z6eLYc";

    // Construire le texte des notes
    $notes_text = "";
    foreach ($notes as $matiere => $note) {
        if (!in_array($matiere, ['id', 'matricule', 'nom', 'prenom'])) {
            $notes_text .= ucwords(str_replace('_', ' ', $matiere)) . ": " . $note . ", ";
        }
    }
    $notes_text = rtrim($notes_text, ", ");

    // Définir la question
    $question = "Peux-tu me produire un mail professionnel, concis et sans partie à remplir pour donner un conseil d'orientation à un étudiant dont le nom est $nom, le prénom $prenom, et dont les notes sont les suivantes : $notes_text. Le mail sera adressé aux parents. Le nom de l'établissement c'est Keyce Informatique et IA et le message est envoyé de la part de la scolarité de l'établissement. Sois clair et dit dans quelle filiere il devrat pointer dans le message et utilise un langage professionnel.";

    // Construire l'URL de l'API
    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=" . $GKey;

    // Préparer les données de la requête
    $requestData = json_encode([
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $question]
                ]
            ]
        ]
    ]);

    // Initialiser cURL
    $ch = curl_init($url);

    // Configurer les options cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);

    // Désactiver la vérification SSL (utiliser uniquement pour les tests)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Envoyer la requête et récupérer la réponse
    $response = curl_exec($ch);

    // Vérifier les erreurs cURL
    if (curl_errno($ch)) {
        die("Erreur cURL : " . curl_error($ch));
    }

    // Fermer la connexion cURL
    curl_close($ch);

    // Décoder la réponse JSON
    $responseObject = json_decode($response, true);

    // Vérifier si des candidats existent dans la réponse
    if (isset($responseObject['candidates']) && count($responseObject['candidates']) > 0) {
        return $responseObject['candidates'][0]['content'] ?? "Aucun contenu généré.";
    } else {
        return "Aucun candidat trouvé dans la réponse JSON.";
    }
}

// Fonction pour envoyer un email avec PHPMailer
// Fonction pour envoyer un email (exemple)
function sendEmail($to, $cc, $nom, $prenom, $attachment, $orientation_text) {
    // Vérification et conversion des données
    $convertedText = '';

    // Vérifiez si $orientation_text est un tableau
    if (is_array($orientation_text)) {
        // Vérifiez si une clé spécifique, comme 'role', contient une chaîne
        if (isset($orientation_text['role']) && is_string($orientation_text['role'])) {
            $convertedText = nl2br($orientation_text['role']);
        } else {
            // Gérez les erreurs si les données ne sont pas comme attendu
            $convertedText = "Erreur : Le champ 'role' n'est pas défini ou n'est pas une chaîne.";
        }
    } elseif (is_string($orientation_text)) {
        // Si $orientation_text est déjà une chaîne
        $convertedText = nl2br($orientation_text);
    } else {
        // Gestion d'autres types de données
        $convertedText = "Erreur : Données invalides pour orientation_text.";
    }

    // Construction du corps de l'email (exemple simplifié)
    $subject = "Votre relevé de notes";
    $message = "Bonjour $prenom $nom,<br><br>";
    $message .= "Veuillez trouver ci-joint votre relevé de notes.<br><br>";
    $message .= "Informations complémentaires :<br>";
    $message .= $convertedText;

    // Ajout des pièces jointes et des en-têtes (non implémenté ici)
    // Exemple d'envoi
    mail($to, $subject, $message, "From: $cc");

    echo "Email envoyé à $to avec succès.";
}
?>
