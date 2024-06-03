<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $Name = $_GET["name"];
    try {
        require_once "dbh.inc.php";
        $query = "SELECT Name FROM transactions WHERE Name = :Name";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":Name", $Name);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            echo json_encode(['status' => 1, 'message' => 'User exists', 'data' => $res]);
        } else {
            echo json_encode(['status' => 2, 'message' => 'User does not exist']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 0, 'error' => $e->getMessage()]);
    } finally {
        $pdo = null;
        $stmt = null;
    }
}
?>