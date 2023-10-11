<?php
require "userClass.php";

$inputJSON = $_POST["data"];
$data = json_decode($inputJSON, true);

if (isset($data["token"]) && isset($data["userID"]) && isset($data["password"])) {
    $token = $data["token"];
    $userID = $data["userID"];
    $password = $data["password"];
    $userPdo = new userPdo();
    try {
        $userIDFromToken = $userPdo->validateTokenLogin($token);
        if ($userID == $userIDFromToken) {
            try {
                $deleteSuccess = $userPdo->deleteUser($userID, $password);
                if ($deleteSuccess == true) {
                    $response = [
                        "result" => true
                    ];
                } else {
                    $response = [
                        "result" => false,
                        "errorCode" => "Password Incorrect."
                    ];
                }
            } catch (PDOException $ex) {
                $response = [
                    "result" => false,
                    "errorCode" => "Database error: " . $ex->getMessage()
                ];
            }
        } else {
            $response = [
                "result" => false,
                "errorCode" => "Token invalid for account."
            ];
        }
    } catch (PDOException $ex) {
        $response = [
            "result" => false,
            "errorCode" => "Database error: " . $ex->getMessage()
        ];
    }

} else {
    if (isset($data["token"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "Token not provided."
        ];
    } else if (isset($data["userID"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "UserID not provided."
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