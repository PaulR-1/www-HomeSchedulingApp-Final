<?php
class UserModel
{
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

public function createUser($firstName, $lastName, $email, $password): bool{
    $insertQuery = "INSERT INTO users_tbl(firstName, lastName, email, password, createdAt, updatedAt)
                    VALUES (:firstName, :lastName, :email, :password, :createdAt, :updatedAt)";
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

    // // $passwordCheck = password_verify($password, $hashedPassword);

    $dateNow = date('Y-m-d H:i:s');
    $response = $this->conn->prepare($insertQuery);
    $response->bindParam(":firstName", $firstName);
    $response->bindParam(":lastName", $lastName);
    $response->bindParam(":email", $email);
    $response->bindParam(":password", $hashedPassword);
    $response->bindParam(":createdAt", $dateNow);
    $response->bindParam(":updatedAt", $dateNow);

    return $response->execute();
}

public function updateUser($userID, $firstName, $lastName, $email, $password, $homeID)
{
    $updateQuery = "UPDATE users_tbl 
                    SET firstName = :firstName, 
                        lastName = :lastName, 
                        email = :email,
                        password = :password,
                        homeID = :homeID, 
                        updatedAt = :updatedAt
                    WHERE userID = :userID";
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

    $response = $this->conn->prepare($updateQuery);

    $dateNow = date('Y-m-d H:i:s');
    $response->bindParam(":firstName", $firstName);
    $response->bindParam(":lastName", $lastName);
    $response->bindParam(":email", $email);
    $response->bindParam(":password", $hashedPassword);
    $response->bindParam(":homeID", $homeID);
    $response->bindParam(":updatedAt", $dateNow);
    $response->bindParam(":userID", $userID);

    return $response->execute();
}

    public function readUser()
    {
        $selectQuery = "SELECT * FROM users_tbl";
        $response = $this->conn->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    public function readAdvancedUser()
    {
        $selectQuery = "SELECT users_tbl.*, homes_tbl.homeName
                        FROM users_tbl
                        LEFT JOIN homes_tbl ON users_tbl.homeID = homes_tbl.homeID";
        $response = $this->conn->prepare($selectQuery);
        $response->execute();
        return $response;
    }



    public function loginHome($userID, $homeID): bool
    {
        $updateQuery = "UPDATE users_tbl
                        SET homeID = :homeID,
                            updatedAt = :updatedAt
                        WHERE userID = :userID";

        $response = $this->conn->prepare($updateQuery);

        $dateNow = date('Y-m-d H:i:s');
        $response->bindParam(":homeID", $homeID);
        $response->bindParam(":updatedAt", $dateNow);
        $response->bindParam(":userID", $userID);

        return $response->execute();
    }

    public function findUserByEmailAndPassword($email, $password)
    {
        // $selectQuery = "SELECT * FROM users_tbl WHERE email = :email AND password = :password LIMIT 1";
        // $response = $this->conn->prepare($selectQuery);
        // $response->bindParam(":email", $email);
        // $response->bindParam(":password", $password);
        // $response->execute();
        // return $response->fetch(PDO::FETCH_ASSOC);

        $selectQuery = "SELECT * FROM users_tbl WHERE email = :email LIMIT 1";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":email", $email);
        $response->execute();

        $user = $response->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password']))
            {
                return $user;
            }
        
        return false;
    }

    public function deleteUser($userID)
    {
        $deleteQuery = "DELETE FROM users_tbl WHERE userID = :userID";
        $response = $this->conn->prepare($deleteQuery);
        $response->bindParam(":userID", $userID);
        $response->execute();
        return $response;
    }
    //Ask sir tommorow if dito talga toh
    public function getUserByID($userID)
    {
        $selectQuery = "SELECT * FROM users_tbl WHERE userID = :userID LIMIT 1";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":userID", $userID, PDO::PARAM_INT);
        $response->execute();
        return $response->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email)
    {
        $selectQuery = "SELECT * FROM users_tbl WHERE email = :email LIMIT 1";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":email", $email);
        $response->execute();
        return $response->fetch(PDO::FETCH_ASSOC);
    }

    public function getConcurrentUsersByHomeID($homeID)
    {
        $selectQuery = "SELECT userID, firstName, lastName, email, roleName
                        FROM users_tbl
                        WHERE homeID = :homeID
                        ORDER BY firstName ASC, lastName ASC";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        $response->execute();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAdminsByHomeID($homeID)
    {
        $selectQuery = "SELECT COUNT(*) AS total_admins
                        FROM users_tbl
                        WHERE homeID = :homeID
                        AND roleName = 'Admin'";
        $response = $this->conn->prepare($selectQuery);
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        $response->execute();
        return $response->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUserRoleByHome($userID, $roleName, $homeID): bool
    {
        $updateQuery = "UPDATE users_tbl
                        SET roleName = :roleName,
                            updatedAt = :updatedAt
                        WHERE userID = :userID
                        AND homeID = :homeID";
        $response = $this->conn->prepare($updateQuery);
        $dateNow = date('Y-m-d H:i:s');
        $response->bindParam(":roleName", $roleName);
        $response->bindParam(":updatedAt", $dateNow);
        $response->bindParam(":userID", $userID, PDO::PARAM_INT);
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        return $response->execute();
    }

    public function addRoleLog($targetUserID, $oldRoleName, $newRoleName, $changedByUserID, $homeID): bool
    {
        try{
            $targetUser = $this->getUserByID($targetUserID);
            $targetName = "user #" . (int)$targetUserID;
            if($targetUser){
                $targetName = trim((string)$targetUser["firstName"] . " " . (string)$targetUser["lastName"]);
                if($targetName == ""){
                    $targetName = "user #" . (int)$targetUserID;
                }
            }

            $details = "Changed " . $targetName . " from " . $oldRoleName . " to " . $newRoleName;
            $insertQuery = "INSERT INTO logs_tbl(userID, homeID, action, details, createdAt, updatedAt)
                            VALUES (:userID, :homeID, :action, :details, :createdAt, :updatedAt)";
            $response = $this->conn->prepare($insertQuery);
            $dateNow = date('Y-m-d H:i:s');
            $action = "role_update";
            $response->bindParam(":userID", $changedByUserID, PDO::PARAM_INT);
            $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
            $response->bindParam(":action", $action);
            $response->bindParam(":details", $details);
            $response->bindParam(":createdAt", $dateNow);
            $response->bindParam(":updatedAt", $dateNow);
            return $response->execute();
        }catch(PDOException $ex){
            return false;
        }
    }

    public function getRecentRoleLogsByHomeID($homeID, $limit = 8)
    {
        try{
            $selectQuery = "SELECT l.createdAt, l.details,
                                u.firstName AS actorFirstName, u.lastName AS actorLastName
                            FROM logs_tbl l
                            LEFT JOIN users_tbl u ON l.userID = u.userID
                            WHERE l.homeID = :homeID
                            AND l.action = 'role_update'
                            ORDER BY l.createdAt DESC
                            LIMIT :limitRows";
            $response = $this->conn->prepare($selectQuery);
            $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
            $response->bindParam(":limitRows", $limit, PDO::PARAM_INT);
            $response->execute();
            $rows = $response->fetchAll(PDO::FETCH_ASSOC);

            $mappedRows = [];
            foreach($rows as $row){
                $actorName = trim((string)$row["actorFirstName"] . " " . (string)$row["actorLastName"]);
                $detailsText = isset($row["details"]) ? trim((string)$row["details"]) : "";
                if($detailsText == ""){
                    $detailsText = "Role update activity recorded.";
                }

                if($actorName != ""){
                    $detailsText = $actorName . " " . lcfirst($detailsText) . ".";
                }

                $mappedRows[] = [
                    "createdAt" => $row["createdAt"],
                    "actionText" => $detailsText
                ];
            }

            return $mappedRows;
        }catch(PDOException $ex){
            return [];
        }
    }

    public function countRoleUpdateLogsByHomeID($homeID)
    {
        try{
            $selectQuery = "SELECT COUNT(*) AS total_logs
                            FROM logs_tbl
                            WHERE homeID = :homeID
                            AND action = 'role_update'";
            $response = $this->conn->prepare($selectQuery);
            $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
            $response->execute();
            return $response->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $ex){
            return [
                "total_logs" => 0
            ];
        }
    }

    public function assignUserToHomeByEmail($email, $homeID): bool
    {
        $updateQuery = "UPDATE users_tbl
                        SET homeID = :homeID,
                            roleName = 'Admin',
                            updatedAt = :updatedAt
                        WHERE email = :email";
        $response = $this->conn->prepare($updateQuery);
        $dateNow = date('Y-m-d H:i:s');
        $response->bindParam(":homeID", $homeID, PDO::PARAM_INT);
        $response->bindParam(":updatedAt", $dateNow);
        $response->bindParam(":email", $email);
        return $response->execute();
    }

    public function getTotalUsers()
    {
        $selectQuery = "SELECT COUNT(*) AS total_users FROM users_tbl";
        $response = $this->conn->prepare($selectQuery);
        $response->execute();
        return $response->fetch(PDO::FETCH_ASSOC);
    }
}
?>