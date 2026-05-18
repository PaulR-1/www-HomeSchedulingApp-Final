<?php
session_start();
require_once "../bl/UserManager.php";

$usermanager = new UserManager();

if(isset($_POST["adminAction"]) && $_POST["adminAction"] == "updateUserRole"){
    header("Content-Type: application/json");
    $targetUserID = isset($_POST["targetUserID"]) ? (int)$_POST["targetUserID"] : 0;
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 0;
    $roleName = isset($_POST["roleName"]) ? $_POST["roleName"] : "";
    $changedByUserID = isset($_SESSION["userID"]) ? (int)$_SESSION["userID"] : 0;
    echo json_encode($usermanager->updateUserRoleFunc($targetUserID, $roleName, $homeID, $changedByUserID));
    exit;
}
elseif(isset($_POST["adminAction"]) && $_POST["adminAction"] == "addResidentToHome"){
    header("Content-Type: application/json");
    $email = isset($_POST["residentEmail"]) ? $_POST["residentEmail"] : "";
    $homeID = isset($_POST["homeID"]) ? (int)$_POST["homeID"] : 0;
    echo json_encode($usermanager->assignResidentToHomeFunc($email, $homeID));
    exit;
}
elseif(isset($_POST["fName"], $_POST["lName"], $_POST["email"], $_POST["password"]) && !isset($_POST["uID"])){
    $usermanager->addUserFunc($_POST["fName"], $_POST["lName"], $_POST["email"], $_POST["password"]);
    exit;
}
elseif (isset($_POST["fName"], $_POST["lName"], $_POST["email"], $_POST["password"], $_POST["uID"], $_POST["homeID"])){
    $usermanager->updateUserFunc(
        $_POST["fName"],
        $_POST["lName"],
        $_POST["email"],
        $_POST["password"],
        $_POST["uID"],
        $_POST["homeID"]
    );
}
elseif (isset($_POST["indes"])){
    $usermanager->removeUserFunc($_POST["indes"]);
}
elseif (isset($_POST["LFNAME"], $_POST["LLNAME"], $_POST["LhomeSelect"])){
    $usermanager->loginUserFunc($_POST["LFNAME"], $_POST["LLNAME"], $_POST["LhomeSelect"]);
}
?>