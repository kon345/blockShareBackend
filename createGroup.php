<?php
require "groupClass.php";
require "userClass.php";


$inputJSON = $_POST["data"];
$data = json_decode($inputJSON, true);



if (isset($data["groupName"]) && isset($data["groupCategory"]) && isset($data["creatorID"])) {
    $groupName = $data["groupName"];
    $category = $data["groupCategory"];
    $creatorID = $data["creatorID"];
    $groupPdo = new groupPdo();
    $userPdo = new userPdo();
    $isDuplicateGroupCode = true;
    try {
        while ($isDuplicateGroupCode) {
            $newGroupCode = $groupPdo->generateGroupCode();
            $isDuplicateGroupCode = $groupPdo->checkGroupCodeDuplicate($newGroupCode);
        }
        $groupPdo->createGroup($groupName, $category, $creatorID, $newGroupCode);
        $groupID = $groupPdo->getGroupIDbyCode($newGroupCode);
        $userPdo->joinGroup($groupID, $creatorID);
        $response = [
            "result" => true
        ];
        $groupCode = ["groupCode" => $newGroupCode];
    } catch (PDOException $ex) {
        $response = [
            "result" => false,
            "errorCode" => "Database error: " . $ex->getMessage()
        ];
    }
} else {
    if (isset($data["groupName"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "GroupName not provided."
        ];
    } else if (isset($data["groupCategory"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "GroupCategory not provided."
        ];
    } else if (isset($data["creatorID"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "CreatorID not provided."
        ];
    }
}
if (isset($groupCode)) {
    $serverResult = ["response" => $response, "content" => $groupCode];
} else {
    $serverResult = ["response" => $response];
}
echo json_encode($serverResult);
?>