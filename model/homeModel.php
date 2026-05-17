<?php 
    class HomeModel
    {   
        private $conn;
        public function __construct($db){
            $this->conn = $db;
        }

        public function readHome()
        {
            $selectQuery = "SELECT * FROM homes_tbl";
            $response = $this->conn->prepare($selectQuery);
            $response->execute();
            return $response;
        }

        public function cardDepartments() 
        {
            // $selectQuery = "SELECT d.homeName, COUNT(u.userID) AS total_users FROM homes_tbl d LEFT JOIN users_tbl u ON u.homeID = d.homeID GROUP BY d.homeName";
            // $response = $this->conn->prepare($selectQuery);
            // $response->execute();
            // $homes = $response->fetchAll(PDO::FETCH_ASSOC);
            // return $homes;

            $selectQuery = "SELECT d.homeName, COUNT(u.userID) AS total_users 
                    FROM homes_tbl d 
                    LEFT JOIN users_tbl u ON u.homeID = d.homeID 
                    GROUP BY d.homeName";

                    $homes = $this->conn->prepare($selectQuery);
                    $homes->execute();
                    return $homes->fetchAll(PDO::FETCH_ASSOC);

        }

        public function getTotalHomes()
        {
            $selectQuery = "SELECT COUNT(*) AS total_homes FROM homes_tbl";
            $return = $this->conn->prepare($selectQuery);
            $return->execute();
            return $return ->fetch(PDO::FETCH_ASSOC);
        }

    }
?>