<?php
require "groupClass.php";

if (isset($_GET["groupID"]) && isset($_GET["blockID"])) {
    $groupID = $_GET["groupID"];
    $blockID = $_GET["blockID"];
    $groupPdo = new groupPdo();
    try {
        $blockIDList = $groupPdo->getBlockIDsInGroups($groupID, $blockID);
        if (isset($blockIDList) && !empty(($blockIDList))) {
            try {
                $blockList = $groupPdo->getBlocksbyBlockIDs($blockIDList);
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
                "errorCode" => "no Block in group."
            ];
        }
    } catch (PDOException $ex) {
        $response = [
            "result" => false,
            "errorCode" => "Database error: " . $ex->getMessage()
        ];
    }
} else {
    if (isset($_GET["groupID"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "GroupID not provided."
        ];
    } else if (isset($_GET["blockID"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "BlockID not provided."
        ];
    }
}

if (isset($blockList)) {
    $serverResult = ["response" => $response, "content" => $blockList];
} else {
    $serverResult = ["response" => $response];
}
echo json_encode($serverResult);
?>