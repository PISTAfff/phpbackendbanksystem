<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $UserID = $data["userID"];
    $Name = $data["name"];
    $Amount = $data["amount"];
    try {
        require_once "dbh.inc.php";
        $query = "SELECT username,amount FROM users WHERE id = :UserID";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":UserID", $UserID);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $RefKey = ($res['username'] . ":") . $Amount;
        $currentAmount = ((int) $res['amount']) - $Amount;
        $query = "UPDATE users SET Amount = :Amount WHERE id = :UserID";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":Amount", $currentAmount);
        $stmt->bindParam(":UserID", $UserID);
        $stmt->execute();
        $query = "SELECT amount FROM users WHERE username = :Name";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":Name", $Name);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentAmount = (int) $res['amount'];
        $currentAmount += $Amount;
        $query = "UPDATE users SET Amount = :Amount WHERE username = :Name";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":Amount", $currentAmount);
        $stmt->bindParam(":Name", $Name);
        $stmt->execute();

        $query = "UPDATE transactions SET type='Completed Request',Color='Red', state=1 WHERE RefKey=:RefKey";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":RefKey", $RefKey);
        $stmt->execute();

        list($name, $number) = explode(":", $RefKey);
        $name = trim($name);
        $query = "UPDATE transactions SET type='Finsihed Request' ,Color='Green' WHERE name=:Name and Amount=:Amount";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":Name", $name);
        $stmt->bindParam(":Amount", $Amount);
        $stmt->execute();
        echo json_encode(1);
    } catch (PDOException $e) {
        echo json_encode(2);
    } finally {
        $pdo = null;
        $stmt = null;
    }
}
?>