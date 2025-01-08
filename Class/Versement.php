<?php
require_once 'Database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

require 'vendor/autoload.php';

class Versement
{
    private $conn; // Connexion à la base de données
    private $table = "versements";
    // Nom de la table

    // Propriétés de l'étudiant

    public $matricule;
    public $date_versement;
    public $montant;
    public $numero_versement;


    // Constructeur : initialise la connexion à la base de données
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllVersement()
    {
        $query = "SELECT * FROM " . $this->table;
        $result = $this->conn->query($query);

        if ($result === false) {
            return [];
        }

        $etudiants = [];
        while ($row = $result->fetch_assoc()) {
            $etudiants[] = $row;
        }

        return $etudiants;
    }
    public function supprimerVersement($id)
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

    public function ajouterVersement()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (matricule, montant, date_versement, numero_versement) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt) {
            return false;
        }
        
        // Liaison des paramètres
        $stmt->bind_param(
            "ssss",
            $this->matricule,
            $this->montant,
            $this->date_versement,
            $this->numero_versement,
            // $this->email,
            // $this->niveau,
            // $this->montant,
            // $this->nom_parent,
            // $this->email_parent,
            // $this->age
        );

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

    public function envoyerMail($matricule, $montant)
    {
        // Éviter l'injection SQL
        $query = "SELECT * FROM etudiants WHERE matricule = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt) {
            // Lier le paramètre matricule
            $stmt->bind_param("s", $matricule);
            $stmt->execute();

            // Récupérer le résultat
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Récupérer les données de l'étudiant
                $row = $result->fetch_assoc();
                $email = $row['email'];
                $montant1 = $row['montant'];
                $nom = $row['nom'];
                $prenom = $row['prenom'];

                // Calculer le montant restant
                $reste = $montant1 - $montant;

                // Générer le contenu de l'email via Gemini
                $response = $this->gemini($nom, $prenom, $montant, $reste);

                // Envoyer l'email avec PHPMailer
                $this->envoyerMatriculeParEmail($email, $response, $prenom);

                $query1 = "UPDATE etudiants SET montant = ? WHERE matricule = ?";
                $stmt1 = $this->conn->prepare($query1);

                if ($stmt1) {
                    $stmt1->bind_param("ds", $reste, $matricule); // "d" pour un double (montant) et "s" pour une chaîne (matricule)
                    $stmt1->execute();
                } else {
                    echo "Erreur lors de la préparation de la requête : " . $this->conn->error;
                }

                return true;
            } else {
                echo "Aucun étudiant trouvé avec le matricule $matricule.";
                return false;
                echo "Erreur : {$mail->ErrorInfo}";
            }

            $stmt->close();
        } else {
            echo "Erreur lors de la préparation de la requête : " . $this->conn->error;
        }
    }

    public function gemini($nom, $prenom, $montantPayer, $reste)
    {
        // Clé API
        $GKey = "AIzaSyCs-JPy-wUcZtWS_RmdUljfCI27oCiEjwA";

        // Définir la question
        $question = "Peux-tu me produire un mail professionnel, concis et sans partie à remplir pour décrire la situation financière d'un étudiant dont le nom est $nom, le prénom $prenom, qui vient de payer une somme de $montantPayer FCFA. Le reste à payer est de $reste FCFA. Le mail sera adressé aux parents.Le nom de l etablissement c est keyce informatique et IA et le message est envoyer de la part de la scolarite de l etablissemant. sois un peu plus large dans le msessage et ulilisent un language professionel";

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
            // Obtenir le contenu du premier candidat
            $content = $responseObject['candidates'][0]['content'] ?? null;

            // Vérifier si le contenu existe
            if ($content && isset($content['parts']) && count($content['parts']) > 0) {
                return $content['parts'][0]['text'];
            } else {
                return "Aucune partie trouvée dans le contenu sélectionné.";
            }
        } else {
            return "Aucun candidat trouvé dans la réponse JSON.";
        }
    }

    public function envoyerMatriculeParEmail($email, $message, $nom)
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
            $mail->addAddress($email, "");

            // Contenu de l'e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Situation financiere de ' . $nom;
            $mail->Body = $message;

            // Envoi de l'e-mail
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Erreur : {$mail->ErrorInfo}";
            return false;
        }
    }
}
