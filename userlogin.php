<?php
require "userClass.php";


$inputJSON = $_POST["data"];
$data = json_decode($inputJSON, true);

if (isset($data["account"]) && isset($data["password"])) {
    $account = $data["account"];
    $password = $data["password"];
    $userPdo = new userPdo();
    // 登入取得userID
    try {
        $userID = $userPdo->login($account, $password);
    } catch (PDOException $ex) {
        $response = [
            "result" => false,
            "errorCode" => "Database error1: " . $ex->getMessage()
        ];
    }

    if ($userID != false) {
        // userID檢查是否曾經有token
        try {
            $hasLogined = $userPdo->checkUserHasLogined($userID);
        } catch (PDOException $ex) {
            $response = [
                "result" => false,
                "errorCode" => "Database error2: " . $ex->getMessage()
            ];
        }

        $isDuplicateToken = true;
        // update token
        if ($hasLogined) {
            try {
                while ($isDuplicateToken) {
                    $newToken = $userPdo->generateToken();
                    $isDuplicateToken = $userPdo->checkTokenDuplicate($newToken);
                }
                $userPdo->updateToken($userID, $newToken);
                $response = ["result" => true];
                $userInfo = ["userID" => $userID, "token" => $newToken];
            } catch (PDOException $ex) {
                $response = [
                    "result" => false,
                    "errorCode" => "Database error3: " . $ex->getMessage()
                ];
            }
        }
        // 創新token資料
        else {
            try {
                while ($isDuplicateToken) {
                    $newToken = $userPdo->generateToken();
                    $isDuplicateToken = $userPdo->checkTokenDuplicate($newToken);
                }
                $userPdo->createNewToken($userID, $newToken);
                $response = ["result" => true];
                $userInfo = ["userID" => $userID, "token" => $newToken];
            } catch (PDOException $ex) {
                $response = [
                    "result" => false,
                    "errorCode" => "Database error4: " . $ex->getMessage()
                ];
            }
        }
    } else {
        $response = [
            "result" => false,
            "errorCode" => "Account or Password Incorrect."
        ];
    }

} else {
    if (isset($data["account"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "Account not provided."
        ];
    } else if (isset($data["password"]) == false) {
        $response = [
            "result" => false,
            "errorCode" => "Password not provided."
        ];
    }
}

if (isset($userInfo)) {
    $serverResult = ["response" => $response, "content" => $userInfo];
} else {
    $serverResult = ["response" => $response];
}
echo json_encode($serverResult);
?>