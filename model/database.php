<?php
    class Database{
        private $host = "localhost";
        private $dbName = "homeplannerdb";
        private $username = "root";
        private $password = "";

        public function connectDB()
        {
            try{
                $conn = new PDO(
                    "mysql:host=$this->host;port=3306;dbname=$this->dbName",
                    $this->username,
                    $this->password
                );

                $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                return $conn;
            }catch(PDOException $e) 
            {
                echo "Somethings wrong with the sql connection". $e->getMessage();
            }
        }
    }
?>