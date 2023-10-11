<?php
require "groupClass.php";

$inputJSON = $_POST["data"];
$data = json_decode($inputJSON, true);

if (isset($data["blockContent"]) && isset($data["blockCategoryIndex"]) && isset($data["blockPosition"]) && isset($data["groupID"])) {
    $blockContent = $data["blockContent"];
    $blockCategoryIndex = $data["blockCategoryIndex"];
    $blockPosition = $data["blockPosition"];
    $groupID = $data["groupID"];
    $groupPdo = new groupPdo();
    $isDuplicatePosition = false;
    try {
        foreach ($blockPosition as $position) {
            $column = $position[0];
            $row = $position[1];
            $isDuplicatePosition = $groupPdo->checkBlockPositionDuplicate($column, $row, $groupID);
            if ($isDuplicatePosition) {
                $response = [
                    "result" => false,
                    "errorCode" => "Position is occupied."
                ];
                break;
            }
        }

        if ($isDuplicatePosition == false) {
            try {
                $newBlockID = $groupPdo->createBlock($blockContent, $blockCategoryIndex, $blockPosition);
                $groupPdo->saveBlockGroup($groupID, $newBlockID);
                foreach ($blockPosition as $position) {
                    $column = $position[0];
                    $row = $position[1];
                    $groupPdo->saveBlockPosition($newBlockID, $column, $row, $groupID);
                }
                $response = [
                    "result" => true
                ];
                $blockID = ["blockID" => $newBlockID];
            } catch (PDOException $ex) {
                $response = [
                    "result" => false,
                    "errorCode" => "Database error: " . $ex->getMessage()
                ];
            }
        }
    } catch (PDOException $ex) {
        $response = [
            "result" => false,
            "errorCode" => "Database error: " . $ex->getMessage()
        ];
    }
} else {
    if (isset($data["blockContent"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "BlockContent not provided."
        ];
    } else if (isset($data["blockCategoryIndex"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "BlockCategoryIndex not provided."
        ];
    } else if (isset($data["blockPosition"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "BlockPosition not provided."
        ];
    } else if (isset($data["groupID"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "GroupID not provided."
        ];
    }
}
if (isset($blockID)) {
    $serverResult = ["response" => $response, "content" => $blockID];
} else {
    $serverResult = ["response" => $response];
}
echo json_encode($serverResult);
?>