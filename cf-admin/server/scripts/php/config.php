<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 're_revamps';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
