<?php
require_once 'Database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Etudiant
{
    private $conn; // Connexion à la base de données
    private $table = "etudiants";
    private $table1 = "connexion"; // Nom de la table

    // Propriétés de l'étudiant
    public $nom;
    public $prenom;
    public $matricule;
    public $photo;
    public $email;
    public $niveau;
    public $montant;
    public $age;
    public $reste;
    public $montant_paye;
    public $statut;
    public $date_naissance;

    // Constructeur : initialise la connexion à la base de données
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Méthode pour ajouter un étudiant
    public function ajouterEtudiant()
    {
        $query = "INSERT INTO " . $this->table . "
                  (nom, prenom, matricule, photo, email, niveau, montant, age, montant_paye, reste, statut, date_naissance)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt) {
            return false;
        }

        // Liaison des paramètres
        $stmt->bind_param(
            "ssssssdiddss",
            $this->nom,
            $this->prenom,
            $this->matricule,
            $this->photo,
            $this->email,
            $this->niveau,
            $this->montant,
            $this->age,
            $this->montant_paye,
            $this->reste,
            $this->statut,
            $this->date_naissance
        );

        // Exécute la requête et retourne le résultat
        return $stmt->execute();
    }

    public function ajouterEtudiantCon()
    {
        $query1 = "INSERT INTO " . "connexion" . "
                  (matricule, password)
                  VALUES (?, ?)";

        $stmt1 = $this->conn->prepare($query1);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt1) {
            return false;
        }

        // Liaison des paramètres
        $stmt1->bind_param(
            "ss",
            $this->matricule,
            $this->matricule
        );

        // Exécute la requête et retourne le résultat
        return $stmt1->execute();
    }

    // Méthode pour récupérer tous les étudiants avec filtrage
    public function getAllEtudiants($search = '', $filter = 'nom')
    {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";

        if (!empty($search)) {
            switch ($filter) {
                case 'nom':
                    $query .= " AND nom LIKE ?";
                    break;
                case 'niveau':
                    $query .= " AND niveau LIKE ?";
                    break;
                case 'matricule':
                    $query .= " AND matricule LIKE ?";
                    break;
                default:
                    $query .= " AND nom LIKE ?";
            }
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($search)) {
            $search = "%$search%";
            $stmt->bind_param('s', $search);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $etudiants = [];
        while ($row = $result->fetch_assoc()) {
            $etudiants[] = $row;
        }

        return $etudiants;
    }

    // Méthode pour mettre à jour un étudiant
    public function mettreAJourEtudiant()
    {
        $query = "UPDATE " . $this->table . "
                  SET nom = ?, prenom = ?, photo = ?, email = ?, niveau = ?, montant = ?, age = ?
                  WHERE matricule = ?";

        $stmt = $this->conn->prepare($query);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt) {
            return false;
        }

        // Liaison des paramètres
        $stmt->bind_param(
            "sssssdss",
            $this->nom,
            $this->prenom,
            $this->photo,
            $this->email,
            $this->niveau,
            $this->montant,
            $this->age,
            $this->matricule
        );

        // Exécution de la requête
        return $stmt->execute();
    }

    public function getEtudiantById($id) {
        $query = "SELECT * FROM etudiants WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateEtudiant($id, $nom, $prenom, $matricule, $email, $niveau, $date_naissance, $montant, $photo) {
        $query = "UPDATE etudiants SET nom = ?, prenom = ?, matricule = ?, email = ?, niveau = ?, date_naissance = ?, montant = ?, photo = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssssdssi', $nom, $prenom, $matricule, $email, $niveau, $date_naissance, $montant, $photo, $id);
        return $stmt->execute();
    }

    // Méthode pour supprimer un étudiant
    public function supprimerEtudiant($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);

        // Exécute la requête et retourne le résultat
        return $stmt->execute();
    }

    public function supprimerConnexion($id)
    {
        $query = "DELETE FROM connexion WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);

        // Exécute la requête et retourne le résultat
        return $stmt->execute();
    }

    public function obtenirEtudiantParId($matricule)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE matricule = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function obtenirEtudiantParMatricule($matricule)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE matricule = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function verifierConnexion($matricule, $password)
    {
        $query = "SELECT * FROM connexion WHERE matricule = ? AND password = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $matricule, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Fonction pour envoyer la carte d'étudiant par email
    public function envoyerCarteParEmail($nom, $prenom, $date_naissance, $matricule, $email, $photo_path, $conn)
    {
        // Générer le PDF de la carte d'étudiant
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Carte d\'Etudiant', 0, 1, 'C');  // Titre en grand
        $pdf->Ln(10);  // Un espace après le titre
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Cell(0, 10, 'Keyce informatique & intelligence artificielle', 0, 1, 'C');  // Sous-titre
        $pdf->Image('logo.jpeg', 10, 10, 30);  // Logo à gauche (assurez-vous que le fichier logo.jpeg existe)
        $pdf->Ln(20);  // Un espace après le sous-titre

        // Informations de l'étudiant
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, "Nom: $nom $prenom", 0, 1);
        $pdf->Cell(0, 10, "Date de naissance: $date_naissance", 0, 1);
        $pdf->Cell(0, 10, "Matricule: $matricule", 0, 1);
        $pdf->Ln(10);  // Un espace avant la photo
        $pdf->Image($photo_path, 150, 60, 30);  // Afficher la photo à droite (position x = 150 et y = 60)

        // Enregistrer le fichier PDF dans le répertoire 'uploads'
        $pdf_path = 'uploads/' . $matricule . '_carte.pdf';
        $pdf->Output('F', $pdf_path);  // Sauvegarder le fichier PDF

        // Envoyer l'email avec la carte d'étudiant en pièce jointe
        $mail = new PHPMailer(true);
        try {
            // Configuration SMTP pour Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Hôte SMTP de Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'mininoulilou@gmail.com'; // Votre adresse e-mail
            $mail->Password = 'hpqz zoke gzcq bkfb'; // Remplacez par un mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Expéditeur et destinataire
            $mail->setFrom('mininoulilou@gmail.com', 'Keyce Admin');
            $mail->addAddress($email, "$nom $prenom"); // L'email de l'étudiant

            // Vérification que le fichier PDF existe
            if (file_exists($pdf_path)) {
                $mail->addAttachment($pdf_path); // Attache le fichier PDF
            } else {
                throw new Exception("Le fichier PDF spécifié n'existe pas : $pdf_path");
            }

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Votre carte d\'étudiant';
            $mail->Body    = 'Bonjour ' . htmlspecialchars($prenom) . ', voici votre carte d\'étudiant.';

            // Envoyer l'email
            $mail->send();
            echo "Carte d'étudiant envoyée à $email.<br>";
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : " . htmlspecialchars($mail->ErrorInfo) . "<br>";
            echo "Détails de l'erreur : " . htmlspecialchars($e->getMessage()) . "<br>";
        }
    }

    public function envoyerMatriculeParEmail($matricule, $email, $nom_parent)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Serveur SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'mininoulilou@gmail.com'; // Adresse e-mail de l'expéditeur
            $mail->Password = 'hpqz zoke gzcq bkfb';    // Mot de passe de l'expéditeur
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
            $mail->Port = 465; // Port SSL

            // Expéditeur et destinataire
            $mail->setFrom('mininoulilou@gmail.com', 'Keyce Informatique et IA');
            $mail->addAddress($email, $nom_parent);

            // Contenu de l'e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Matricule de l\'etudiant';
            $mail->Body = '
            <p>Bonjour ' . htmlspecialchars($nom_parent) . ',</p>
            <p>Veuillez recevoir ci-joint votre matricule :</p>
            <h1 style="color: blue;">' . htmlspecialchars($matricule) . '</h1>
            <p>Cordialement,<br>L\'équipe de Keyce Admin</p>
        ';

            // Envoi de l'e-mail
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Pour faciliter le débogage, vous pouvez activer la ligne suivante pour afficher l'erreur
            echo "Erreur : {$mail->ErrorInfo}";
            return false;
        }
    }
}
