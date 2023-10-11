<?php

class groupPdo
{

    private $pdo;

    function __construct()
    {
        $this->pdo = new PDO(
            "mysql:host=localhost; dbname=blockShareData; charset=utf8",
            "kon345",
            "b10304005",
            array(
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true
            )
        );
    }

    function createGroup($groupName, $category, $creatorID, $groupCode)
    {
        $sql = "INSERT INTO groups (groupName, creatorID, category, memberCount, groupCode) VALUES (:groupName, :creatorID, :category, :memberCount, :groupCode)";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':groupName', $groupName);
        $pdoStmt->bindValue(':creatorID', $creatorID);
        $pdoStmt->bindValue(':category', json_encode($category));
        $pdoStmt->bindValue(':memberCount', 1);
        $pdoStmt->bindValue(':groupCode', $groupCode);
        $pdoStmt->execute();
    }

    function getGroupIDbyCode($groupCode)
    {
        $sql = "SELECT groupID from groups WHERE groupCode = :groupCode";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':groupCode', $groupCode);
        $pdoStmt->execute();
        $result = $pdoStmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['groupID'])) {
            return $result['groupID'];
        } else {
            return null;
        }
    }

    function joinGroup($groupID, $userID)
    {
        $sql = "INSERT INTO group_users (groupID, userID) VALUES (:groupID, :userID)";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':groupID', $groupID);
        $pdoStmt->bindValue(':userID', $userID);
        $pdoStmt->execute();
    }

    function getGroupListbyUserID($userID)
    {
        $sql = "SELECT * from groups g JOIN group_users gp ON g.groupID = gp.groupID WHERE gp.userID = :userID";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':userID', $userID);
        $pdoStmt->execute();
        $result = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function generateGroupCode()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        while (strlen($randomString) < 4) {
            $randomCharacter = $characters[rand(0, $charactersLength - 1)];
            if (strpos($randomString, $randomCharacter) === false) {
                $randomString .= $randomCharacter;
            }
        }

        return $randomString;
    }

    function checkGroupCodeDuplicate($groupCode)
    {
        $sql = "SELECT COUNT(*) FROM groups WHERE groupCode = :groupCode";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':groupCode', $groupCode);
        $pdoStmt->execute();
        $result = $pdoStmt->fetchColumn();
        if ($result == 0) {
            return false;
        }
        return true;
    }

    function addGroupCodeMemberCount($groupID)
    {
        $sql = "UPDATE groups SET memberCount = memberCount + 1 WHERE groupID = :groupID";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':groupID', $groupID);
        $pdoStmt->execute();
    }

    function checkBlockPositionDuplicate($column, $row, $groupID)
    {
        $sql = "SELECT COUNT(*) FROM blockPosition WHERE positionX = :column AND positionY = :row AND groupID = :groupID";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':column', $column);
        $pdoStmt->bindValue(':row', $row);
        $pdoStmt->bindValue(':groupID', $groupID);
        $pdoStmt->execute();
        $result = $pdoStmt->fetchColumn();
        if ($result == 0) {
            return false;
        }
        return true;
    }

    function createBlock($blockContent, $blockCategoryIndex, $blockPosition)
    {
        $sql = "INSERT INTO blocks (blockContent, blockCategoryIndex, blockPosition) VALUES (:blockContent, :blockCategoryIndex, :blockPosition)";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':blockContent', $blockContent);
        $pdoStmt->bindValue(':blockCategoryIndex', $blockCategoryIndex);
        $pdoStmt->bindValue(':blockPosition', json_encode($blockPosition));
        $pdoStmt->execute();

        $blockID = (int) $this->pdo->lastInsertId();

        return $blockID;
    }

    function saveBlockGroup($groupID, $blockID)
    {
        $sql = "INSERT INTO group_blocks (groupID,blockID) VALUES (:groupID, :blockID)";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':groupID', $groupID);
        $pdoStmt->bindValue(':blockID', $blockID);
        $pdoStmt->execute();
    }

    function saveBlockPosition($blockID, $positionX, $positionY, $groupID)
    {
        $sql = "INSERT INTO blockPosition (blockID,positionX,positionY,groupID) VALUES(:blockID, :positionX, :positionY, :groupID)";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':blockID', $blockID);
        $pdoStmt->bindValue(':positionX', $positionX);
        $pdoStmt->bindValue(':positionY', $positionY);
        $pdoStmt->bindValue(':groupID', $groupID);
        $pdoStmt->execute();
    }

    function getBlockIDsInGroups($groupID, $blockID)
    {
        $sql = "SELECT blockID FROM group_blocks WHERE groupID = :groupID AND blockID > :blockID";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':groupID', $groupID);
        $pdoStmt->bindValue(':blockID', $blockID);
        $pdoStmt->execute();
        $result = $pdoStmt->fetchAll(PDO::FETCH_COLUMN);
        return $result;
    }

    function getBlocksbyBlockIDs($blockIDList)
    {
        $placeholders = implode(',', array_fill(0, count($blockIDList), '?'));
        $pdoStmt = $this->pdo->prepare("SELECT * FROM blocks WHERE blockID IN ($placeholders)");
        $pdoStmt->execute($blockIDList);
        $result = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
?>