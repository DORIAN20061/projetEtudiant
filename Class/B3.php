<?php
require_once 'Database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class B3
{
    private $conn; // Connexion à la base de données
    private $table = "b3";
    // private $table1 = "connexion"; // Nom de la table

    // Propriétés de l'étudiant
    public $nom;
    public $prenom;
    public $matricule;

    // Constructeur : initialise la connexion à la base de données
    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function ajouterEtudiant()
    {
        $query = "INSERT INTO " . $this->table . " 
                  (matricule,nom, prenom) 
                  VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Vérifie si la requête a été préparée avec succès
        if (!$stmt) {
            return false;
        }

        // Liaison des paramètres
        $stmt->bind_param(
            "sss",
            $this->matricule,
            $this->nom,
            $this->prenom,


        );

        // Exécute la requête et retourne le résultat
        return $stmt->execute();
    }
}
