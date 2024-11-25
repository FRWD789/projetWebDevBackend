<?php 

class Database {
    private $dbName ='projetdevweb';
    private $dbHost ='localhost';
    private $dbUser ='root';
    private $dbPassword = '';
    
    public function getConnexion() {
        $conn = null;
        $dsn = "mysql:host=".$this->dbHost.";dbname=".$this->dbName;
        try {
            $conn = new PDO($dsn,$this->dbUser,$this->dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
            echo "Error : ".$error->getMessage();
        }
        return $conn;
    }




}




























?>
