<?php

class userPdo
{

    private $pdo;

    function __construct()
    {
        $this->pdo = new PDO(
            "mysql:host=localhost; port=8888; dbname=blockShareData; charset=utf8",
            "root",
            "b10304005",
            array(
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true
            )
        );
    }

    function createUser($username, $account, $pass)
    {
        $sql = "INSERT INTO users (username, account, password) VALUES (:username, :account, :pass)";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':username', $username);
        $pdoStmt->bindValue(':account', $account);
        $pdoStmt->bindValue(':pass', $pass);
        $pdoStmt->execute();
    }

    function joinGroup($groupID, $userID)
    {
        $sql = "INSERT INTO group_users (groupID, userID) VALUES (:groupID, :userID)";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':groupID', $groupID);
        $pdoStmt->bindValue(':userID', $userID);
        $pdoStmt->execute();
    }

    function getUserIDbyAccount($account)
    {
        $sql = "SELECT userID from users WHERE account = :account";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':account', $account);
        $pdoStmt->execute();
        $result = $pdoStmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    function checkAccountDuplicate($account)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE account = :account";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':account', $account);
        $pdoStmt->execute();
        $result = $pdoStmt->fetchColumn();
        return $result;
    }

    function checkTokenDuplicate($token)
    {
        $sql = "SELECT COUNT(*) FROM userTokens WHERE token = :token";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':token', $token);
        $pdoStmt->execute();
        $result = $pdoStmt->fetchColumn();
        if ($result == 0) {
            return false;
        }
        return true;
    }

    function generateToken()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        while (strlen($randomString) < 10) {
            $randomCharacter = $characters[rand(0, $charactersLength - 1)];
            if (strpos($randomString, $randomCharacter) === false) {
                $randomString .= $randomCharacter;
            }
        }

        return $randomString;
    }

    function login($account, $password)
    {
        $sql = "SELECT userID from users WHERE account = :account AND password = :password LIMIT 1";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':account', $account);
        $pdoStmt->bindValue(':password', $password);
        $pdoStmt->execute();
        $userID = $pdoStmt->fetchColumn();
        return $userID;
    }

    function checkUserHasLogined($userID)
    {
        $sql = "SELECT COUNT(*) FROM userTokens WHERE userID = :userID";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':userID', $userID);
        $pdoStmt->execute();
        $result = $pdoStmt->fetchColumn();
        if ($result == 0) {
            return false;
        }
        return true;
    }

    function createNewToken($userID, $token)
    {
        $sql = "INSERT INTO userTokens VALUES(:userID, :token)";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':userID', $userID);
        $pdoStmt->bindValue(':token', $token);
        $pdoStmt->execute();
    }

    function updateToken($userID, $token)
    {
        $sql = "UPDATE userTokens SET token = :token WHERE userID = :userID";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':userID', $userID);
        $pdoStmt->bindValue(':token', $token);
        $pdoStmt->execute();
    }

    function validateTokenLogin($token)
    {
        $sql = "SELECT userID from userTokens WHERE token = :token";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':token', $token);
        $pdoStmt->execute();
        $userID = $pdoStmt->fetch(PDO::FETCH_ASSOC);
        return $userID !== false ? $userID['userID'] : null;
    }

    function deleteUser($userID, $password)
    {
        $sql = "DELETE FROM users WHERE userID = :userID AND password = :password";
        $pdoStmt = $this->pdo->prepare($sql);
        $pdoStmt->bindValue(':userID', $userID);
        $pdoStmt->bindValue(':password', $password);
        $pdoStmt->execute();

        /// 檢查受影響的行數
        $rowsAffected = $pdoStmt->rowCount();

        return $rowsAffected > 0;
    }
}
?>