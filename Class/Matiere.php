<?php
require_once 'Database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Matiere
{
    private $conn; // Connexion à la base de données
    private $table = "matieres";
    // private $table1 = "connexion"; // Nom de la table

    // Propriétés de l'étudiant
    public $nom_prof;
    public $nom_matiere;
    public $matricule_prof;
    public $niveau_matiere;

    // Constructeur : initialise la connexion à la base de données
    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function ajouterMatiere()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (matricule_prof,nom_prof, nom_matiere, niveau_matiere) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt) {
            return false;
        }

        // Liaison des paramètres
        $stmt->bind_param(
            "ssss",
            $this->matricule_prof,
            $this->nom_prof,
            $this->nom_matiere,
            $this->niveau_matiere,


        );

        // Exécute la requête et retourne le résultat
        return $stmt->execute();
    }

    public function getAllMatiere()
    {
        $query = "SELECT * FROM " . $this->table;
        $result = $this->conn->query($query);

        if ($result === false) {
            return [];
        }

        $matieres = [];
        while ($row = $result->fetch_assoc()) {
            $matieres[] = $row;
        }

        return $matieres;
    }

    public function getAllMatiereParProf($matricule)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE matricule_prof = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getAllMatiereParEtu($niveau)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE niveau_matiere = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $niveau);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }


    public function supprimerMatiere($id)
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
}
