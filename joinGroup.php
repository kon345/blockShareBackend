<?php
require "groupClass.php";
require "userClass.php";

$inputJSON = $_POST["data"];
$data = json_decode($inputJSON, true);

if (isset($data["userID"]) && isset($data["groupCode"])) {
    $userID = $data["userID"];
    $groupCode = $data["groupCode"];
    $groupPdo = new groupPdo();
    $userPdo = new userPdo();
    try {
        $groupID = $groupPdo->getGroupIDbyCode($groupCode);
        if (isset($groupID)) {
            try {
                $userPdo->joinGroup($groupID, $userID);
                $groupPdo->addGroupCodeMemberCount($groupID);
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
            $response = [
                "result" => false,
                "errorCode" => "Invalid Group Code."
            ];
        }
    } catch (PDOException $ex) {
        $response = [
            "result" => false,
            "errorCode" => "Database error: " . $ex->getMessage()
        ];
    }
} else {
    if (isset($data["userID"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "UserID not provided."
        ];
    } else if (isset($data["groupCode"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "GroupCode not provided."
        ];
    }
}
$serverResult = ["response" => $response];
echo json_encode($serverResult);
?>