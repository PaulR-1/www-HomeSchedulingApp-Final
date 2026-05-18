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

    public function getHomeUsersFunc($homeID)
    {
        return $this->userModel->getConcurrentUsersByHomeID($homeID);
    }

    public function getRecentRoleLogsFunc($homeID, $limit = 8)
    {
        return $this->userModel->getRecentRoleLogsByHomeID($homeID, $limit);
    }

public function addUserFunc($firstName, $lastName, $email, $password): void{
    try{
        $existingUser = $this->userModel->getUserByEmail($email);
        if($existingUser){
            http_response_code(409);
            echo "DUPLICATE_EMAIL";
            return;
        }

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
            $_SESSION["userID"] = (int)$user["userID"];
            $_SESSION["homeID"] = (int)$loginSelect;
            $_SESSION["firstName"] = isset($user["firstName"]) ? $user["firstName"] : "";
            $_SESSION["lastName"] = isset($user["lastName"]) ? $user["lastName"] : "";
            $_SESSION["email"] = isset($user["email"]) ? $user["email"] : "";
            $_SESSION["roleName"] = isset($user["roleName"]) ? $user["roleName"] : "";

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

    public function updateUserRoleFunc($targetUserID, $roleName, $homeID, $changedByUserID)
    {
        try{
            $cleanRoleName = trim((string)$roleName);
            if($cleanRoleName != "Admin" && $cleanRoleName != "Member"){
                return [
                    "success" => false,
                    "message" => "Invalid role selected."
                ];
            }

            $targetUser = $this->userModel->getUserByID($targetUserID);
            if(!$targetUser){
                return [
                    "success" => false,
                    "message" => "Target user was not found."
                ];
            }

            if((int)$targetUser["homeID"] !== (int)$homeID){
                return [
                    "success" => false,
                    "message" => "User does not belong to this home."
                ];
            }

            $oldRoleName = isset($targetUser["roleName"]) && trim((string)$targetUser["roleName"]) != "" ? trim((string)$targetUser["roleName"]) : "Member";
            if($oldRoleName == $cleanRoleName){
                return [
                    "success" => true,
                    "message" => "Role is already set to " . $cleanRoleName . "."
                ];
            }

            if($oldRoleName == "Admin" && $cleanRoleName == "Member"){
                $adminCountRow = $this->userModel->countAdminsByHomeID($homeID);
                $totalAdmins = isset($adminCountRow["total_admins"]) ? (int)$adminCountRow["total_admins"] : 0;
                if($totalAdmins <= 1){
                    return [
                        "success" => false,
                        "message" => "At least one Admin must remain in Red Home."
                    ];
                }
            }

            if($this->userModel->updateUserRoleByHome($targetUserID, $cleanRoleName, $homeID)){
                $this->userModel->addRoleLog($targetUserID, $oldRoleName, $cleanRoleName, $changedByUserID, $homeID);
                if((int)$targetUserID === (int)$changedByUserID){
                    $_SESSION["roleName"] = $cleanRoleName;
                }
                return [
                    "success" => true,
                    "message" => "Role updated successfully."
                ];
            }

            return [
                "success" => false,
                "message" => "Unable to update role right now."
            ];
        }catch(PDOException $ex){
            return [
                "success" => false,
                "message" => $ex->getMessage()
            ];
        }
    }

    public function assignResidentToHomeFunc($email, $homeID)
    {
        try{
            $cleanEmail = trim((string)$email);
            if($cleanEmail == ""){
                return [
                    "success" => false,
                    "message" => "Resident email is required."
                ];
            }

            $targetUser = $this->userModel->getUserByEmail($cleanEmail);
            if(!$targetUser){
                return [
                    "success" => false,
                    "message" => "No user found with that email."
                ];
            }

            $homeCode = "#$1001";
            if($homeID == 2){
                $homeCode = "#$1002";
            }
            elseif($homeID == 3){
                $homeCode = "#$1003";
            }
            elseif($homeID == 4){
                $homeCode = "#$1004";
            }
            elseif($homeID == 5){
                $homeCode = "#$FFFFF";
            }

            $displayName = trim((string)$targetUser["firstName"] . " " . (string)$targetUser["lastName"]);
            if($displayName == ""){
                $displayName = "HomePlanner User";
            }

            if((int)$targetUser["homeID"] === (int)$homeID){
                $emailSentResult = sendHomeCodeEmail($cleanEmail, $displayName, $homeCode);
                $message = "Resident is already assigned to this home.";
                if($emailSentResult === true){
                    $message .= " Home ID email sent successfully.";
                }
                else{
                    $message .= " Resident assignment is kept, but email could not be sent.";
                }
                return [
                    "success" => true,
                    "message" => $message
                ];
            }

            if($this->userModel->assignUserToHomeByEmail($cleanEmail, $homeID)){
                $emailSentResult = sendHomeCodeEmail($cleanEmail, $displayName, $homeCode);
                $message = "Resident was assigned to Red Home.";
                if($emailSentResult === true){
                    $message .= " Home ID email sent successfully.";
                }
                else{
                    $message .= " Resident assignment is saved, but email could not be sent.";
                }
                return [
                    "success" => true,
                    "message" => $message
                ];
            }

            return [
                "success" => false,
                "message" => "Unable to assign resident right now."
            ];
        }catch(PDOException $ex){
            return [
                "success" => false,
                "message" => $ex->getMessage()
            ];
        }
    }

}
?>