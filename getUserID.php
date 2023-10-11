<?php
require "userClass.php";
if (isset($_GET["account"])) {
    $account = $_GET["account"];
    $userPdo = new userPdo();
    try {
        $userID = $userPdo->getUserIDbyAccount($account);
        $response = ["result" => true];
    } catch (PDOException $ex) {
        $response = [
            "result" => false,
            "errorCode" => "Database error: " . $ex->getMessage()
        ];
    }
} else {
    $response = [
        "result" => false,
        "errorCode" => "Account not provided. "
    ];
}
if (isset($userID)) {
    $serverResult = ["response" => $response, "content" => $userID];
} else {
    $serverResult = ["response" => $response];
}
echo json_encode($serverResult);
?>