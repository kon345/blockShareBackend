<?php
require "userClass.php";
if (isset($_GET["account"])) {
    $account = $_GET["account"];
    $userPdo = new userPdo();
    try {
        $count = $userPdo->checkAccountDuplicate($account);
        $accountDuplicate = ["accountDuplicate" => ($count > 0) ? true : false];
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
        "errorCode" => "Account not provided."
    ];
}
if (isset($accountDuplicate)) {
    $serverResult = ["response" => $response, "content" => $accountDuplicate];
} else {
    $serverResult = ["response" => $isDuplicated];
}
echo json_encode($serverResult);
?>