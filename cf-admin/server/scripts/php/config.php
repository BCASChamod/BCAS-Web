<?php
$host = '34.195.57.198:3306';
$user = 'dev-conn';
$password = 'IlnEoi*q87N^8ibl';
$dbname = 'sandbox_bcas';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
