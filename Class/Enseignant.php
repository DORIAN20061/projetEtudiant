<?php
require_once 'Database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Enseignant
{
    private $conn; // Connexion à la base de données
    private $table = "enseignants";
    private $table1 = "connexion"; // Nom de la table

    // Propriétés de l'étudiant
    public $nom;
    public $prenom;
    public $matricule;
    public $photo;
    public $email;
    public $fonction;
    public $date_enregistrement;
    

    // Constructeur : initialise la connexion à la base de données
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function ajouterEnseignat()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (matricule,nom, prenom,  photo, email, fonction, date_enregistrement) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt) {
            return false;
        }

        // Liaison des paramètres
        $stmt->bind_param(
            "sssssss",
            $this->matricule,
            $this->nom,
            $this->prenom,            
            $this->photo,
            $this->email,
            $this->fonction,
            $this->date_enregistrement
            
        );

        // Exécute la requête et retourne le résultat
        return $stmt->execute();
    }

    public function ajouterEnseignantCon()
    {
        $query1 = "INSERT INTO " . "connexion_prof" . " 
                  (matricule, password) 
                  VALUES (?, ?)";

        $stmt1 = $this->conn->prepare($query1);
        // $hashedPassword = password_hash($this->matricule, PASSWORD_BCRYPT);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt1) {
            return false;
        }

        // Liaison des paramètres
        $stmt1->bind_param(
            "ss",

            $this->matricule,
            $this->matricule,

        );

        // Exécute la requête et retourne le résultat
        return $stmt1->execute();
    }

    public function getAllEtudiants()
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

    public function supprimerEnseignant($id)
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

    // public function obtenirEtudiantParId($matricule)
    // {
    //     $query = "SELECT * FROM " . $this->table . " WHERE matricule = ?";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bind_param("s", $matricule);
    //     $stmt->execute();
    //     $result = $stmt->get_result();

    //     if ($result->num_rows > 0) {
    //         return $result->fetch_assoc();
    //     }
    //     return null;
    // }

    public function verifierConnexion($matricule, $password)
    {
        $query = "SELECT * FROM connexion_prof WHERE matricule = ? AND password = ?";
        $stmt = $this->conn->prepare($query);
        // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("ss", $matricule, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}