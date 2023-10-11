<?php
require "groupClass.php";
if (isset($_GET["userID"])) {
    $userID = $_GET["userID"];
    $groupPdo = new groupPdo();
    try {
        $groupList = $groupPdo->getGroupListbyUserID($userID);
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
        "errorCode" => "UserID not provided."
    ];
}

if (isset($groupList)) {
    $serverResult = ["response" => $response, "content" => $groupList];
} else {
    $serverResult = ["response" => $response];
}
echo json_encode($serverResult);
?>