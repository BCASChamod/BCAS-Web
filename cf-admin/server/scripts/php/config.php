<?php
$host = '34.195.57.198';
$port = '3306';
$user = 'dev-conn';
$password = 'IlnEoi*q87N^8ibl';
$dbname = 'sandbox_bcas';

$servername = $host;
$username = $user;

$conn = new mysqli("$host:$port", $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'message' => $e->getMessage()
    ]);
    exit;
}


?>
