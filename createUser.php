<?php
require "userClass.php";


$inputJSON = $_POST["data"];
$data = json_decode($inputJSON, true);

if (isset($data["username"]) && isset($data["account"]) && isset($data["password"])) {
    $username = $data["username"];
    $account = $data["account"];
    $password = $data["password"];
    try {
        $userPdo = new userPdo();
        $userPdo->createUser($username, $account, $password);
        $response = [
            "result" => true
        ];
    } catch (PDOException $ex) {
        $response = [
            "result" => false,
            "errorCode" => "Database error: " . $ex->getMessage()
        ];
    }
} else {
    if (isset($data["username"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "Username not provided."
        ];
    } else if (isset($data["account"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "Account not provided."
        ];
    } else if (isset($data["password"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "Password not provided."
        ];
    }
}
$serverResult = ["response" => $response];
echo json_encode($serverResult);
?>