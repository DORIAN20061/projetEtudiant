<?php
// Database.php
class Database {
    private $host = "localhost";
    private $db_name = "etudiants";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        if($this->conn->connect_error) {
            die("Connection failed: ". $this->conn->connect_error);

        }        

        return $this->conn;
    }

    // // test connection
    // public function insererConnexion($matricule, $password, $etudiant_id, $statut) {
    //     $query = "INSERT INTO connexion (matricule, password, etudiant_id, statut) VALUES (:matricule, :password, :etudiant_id, :statut)";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(':matricule', $matricule);
    //     $stmt->bindParam(':password', $password);
    //     $stmt->bindParam(':etudiant_id', $etudiant_id, PDO::PARAM_INT);
    //     $stmt->bindParam(':statut', $statut);
    //     $stmt->execute();
    // }
}


?>