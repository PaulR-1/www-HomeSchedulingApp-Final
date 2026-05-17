<?php
require_once "../helper/send.php";

$name = htmlspecialchars("HomePlanner User");
$email = filter_var("paulroa2006@gmail.com", FILTER_VALIDATE_EMAIL);

if (!$email) {
    die("Invalid email address.");
}

$result = sendSignupEmail($email, $name);

if ($result === true) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email: " . $result;
}

?>
