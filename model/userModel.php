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
    public function getTotalUsers()
    {
        $selectQuery = "SELECT COUNT(*) AS total_users FROM users_tbl";
        $response = $this->conn->prepare($selectQuery);
        $response->execute();
        return $response->fetch(PDO::FETCH_ASSOC);
    }
}
?>