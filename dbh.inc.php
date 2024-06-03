<?php

$dsn = $_ENV["DSN"];
$dbusername = $_ENV["DBUSERNAME"];
$dbpassword = $_ENV["DBPSWD"];
try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["message" => "Connection Failed: " . $e->getMessage()]);
    exit;
} ?>