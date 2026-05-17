<?php
require_once "../model/database.php";
require_once "../model/userModel.php";
require_once "../helper/send.php";

class UserManager
{
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->connectDB();
        $this->userModel = new UserModel($db);
    }

    public function getUser()
    {
        $response = $this->userModel->readUser();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdvancedUser()
    {
        $response = $this->userModel->readAdvancedUser();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

public function addUserFunc($firstName, $lastName, $email, $password): void{
    try{
        if($this->userModel->createUser($firstName, $lastName, $email, $password)){
            $displayName = trim($firstName . " " . $lastName);
            if ($displayName === "") {
                $displayName = "HomePlanner User";
            }

            sendSignupEmail($email, $displayName);
            echo "New User Has Been Added";
        }
        else{
            echo "Error is encountered while adding value to the database";
        }

    }catch(PDOException $ex){
        http_response_code(501);
        echo $ex->getMessage();
        exit;
    }
}

public function updateUserFunc($firstName, $lastName, $email, $password, $userID, $homeID): void{
    try{
        if($this->userModel->updateUser($userID, $firstName, $lastName, $email, $password, $homeID))
        {
            echo "user has been updated";
        }
        else{
            echo "error has occured while updating user";
        }
    }catch(PDOException $ex)
    {
        http_response_code(501);
        echo $ex->getMessage();
        exit;
    }
}

    public function removeUserFunc($userID): void
    {
        try {
            if ($this->userModel->deleteUser($userID)) {
                echo "User has been deleted";
            }
        } catch (PDOException $ex) {
            http_response_code(501);
            echo $ex->getMessage();
            exit;
        }
    }

    public function loginUserFunc($loginEmail, $loginPassword, $loginSelect)
    {
        $user = $this->userModel->findUserByEmailAndPassword($loginEmail, $loginPassword);

        if ($user) {
            $this->userModel->loginHome($user["userID"], $loginSelect);

            if ($loginSelect == 1) {
                echo 1;
            } elseif ($loginSelect == 2) {
                echo 2;
            } elseif ($loginSelect == 3) {
                echo 3;
            } elseif ($loginSelect == 4) {
                echo 4;
            } elseif ($loginSelect == 5) {
                echo 5;
            } else {
                echo 0;
            }
            return;
        }

        echo 0;
    }

}
?>