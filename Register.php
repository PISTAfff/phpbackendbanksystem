<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["username"];
    $email = $data["email"];
    $password = $data["password"];
    try {
        require_once "dbh.inc.php";
        $query = "INSERT INTO users(username,email,password,amount) VALUES(?,?,?,?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$username, $email, $password, 0]);
        $query = "SELECT id FROM users WHERE email=:emailSearch";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":emailSearch", $email);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(["state" => 1, "userid" => $res["id"]]);
    } catch (PDOException $e) {
        echo json_encode(["state" => 2, "userid" => -1]);
    } finally {
        $pdo = null;
        $stmt = null;
    }
}

?>