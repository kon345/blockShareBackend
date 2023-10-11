<?php
require "userClass.php";


$inputJSON = $_POST["data"];
$data = json_decode($inputJSON, true);

if (isset($data["token"])) {
    $token = $data["token"];
    try {
        $userPdo = new userPdo();
        $loginedUserID = $userPdo->validateTokenLogin($token);
        if (isset($loginedUserID)) {
            $response = [
                "result" => true
            ];
            $userID = ["userID" => $loginedUserID];
        } else {
            $response = [
                "result" => false,
                "errorCode" => "Token Invalid, Failed to get userID"
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
        "errorCode" => "Token not provided."
    ];
}
if (isset($userID)) {
    $serverResult = ["response" => $response, "content" => $userID];
} else {
    $serverResult = ["response" => $response];
}
echo json_encode($serverResult);
?>