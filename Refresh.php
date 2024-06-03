<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $email = $_GET["email"];
    try {
        require_once "dbh.inc.php";
        $query = "SELECT id,password,amount,username FROM users WHERE email=:emailSearch";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":emailSearch", $email);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $query = "SELECT Type,Name,Amount,Date,Color,State,UserID FROM transactions WHERE UserID=:id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":id", $res["id"]);
        $stmt->execute();
        $ress = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            "State" => 1,
            "Username" => $res["username"],
            "Amount" => $res["amount"],
            "Transactions" => $ress,
            "id" => $res["id"]
        ]);

    } catch (PDOException $e) {
        echo json_encode(2);
    } finally {
        $pdo = null;
        $stmt = null;
    }
}
?>