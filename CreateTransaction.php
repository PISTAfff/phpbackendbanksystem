<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $UserID = $data["userID"];
    $Type = $data["type"];
    $Name = $data["name"];
    $Amount = $data["amount"];
    $Date = $data["date"];
    $Color = $data["color"];
    $State = $data["sendState"];
    try {
        require_once "dbh.inc.php";
        $query = "INSERT INTO transactions(Type, Name, Amount, Date, Color, State, UserID) VALUES(?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$Type, $Name, $Amount, $Date, $Color, $State, $UserID]);
        $query = "SELECT username,amount FROM users WHERE id = :UserID";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":UserID", $UserID);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentAmount = $res['amount'];
        $senderName = $res["username"];
        switch ($Type) {
            case "Deposit":
                $currentAmount += $Amount;
                $query = "UPDATE users SET Amount = :Amount WHERE id = :UserID";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":Amount", $currentAmount);
                $stmt->bindParam(":UserID", $UserID);
                $stmt->execute();
                break;
            case "Withdraw":
                $currentAmount = $res['amount'] > $Amount ? $res['amount'] - $Amount : $Amount - $res['amount'];
                $query = "UPDATE users SET Amount = :Amount WHERE id = :UserID";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":Amount", $currentAmount);
                $stmt->bindParam(":UserID", $UserID);
                $stmt->execute();
                break;
            case "Send":
                $currentAmount = $res['amount'] - $Amount;
                $query = "UPDATE users SET Amount = :Amount WHERE id = :UserID";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":Amount", $currentAmount);
                $stmt->bindParam(":UserID", $UserID);
                $stmt->execute();

                $query = "SELECT id ,amount FROM users WHERE username = :Name";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":Name", $Name);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $currentAmount = $res['amount'];
                $currentAmount += $Amount;

                $query = "UPDATE users SET Amount = :Amount WHERE id = :UserID";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":Amount", $currentAmount);
                $stmt->bindParam(":UserID", $res["id"]);
                $stmt->execute();

                $query = "INSERT INTO transactions(Type, Name, Amount, Date, Color, State, UserID) VALUES(?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($query);
                $stmt->execute(["Received", $senderName, $Amount, $Date, "Green", 0, $res['id']]);

                break;
            case "OutGoing Request":
                $query = "SELECT id FROM users WHERE username = :Name";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":Name", $Name);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $query = "INSERT INTO transactions(Type, Name, Amount, Date, Color, State, UserID,RefKey) VALUES(?, ?, ?, ?, ?, ?, ?,?)";
                $stmt = $pdo->prepare($query);
                $RefKey = $Name . ":" . $Amount;
                $stmt->execute(["Request", $senderName, $Amount, $Date, "", 0, $res['id'], $RefKey]);
                break;
        }
        echo json_encode(1);
    } catch (PDOException $e) {
        echo json_encode(2);
    } finally {
        $pdo = null;
        $stmt = null;
    }
}
?>