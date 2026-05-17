<?php 
require_once "../model/database.php";
require_once "../model/homeModel.php";
    class HomeManager 
    {
            private $homeModel;
            public function __construct()
            {
                $database = new Database();
                $db = $database->connectDB();
                $this -> homeModel = new HomeModel($db);
            }
            public function getHome()
            {
                $response = $this->homeModel->readHome();
                return $response->fetchAll(PDO::FETCH_ASSOC);
            } 

           
    }










?>