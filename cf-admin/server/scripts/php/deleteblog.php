<?php
require './config.php';
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ./view/login.html");
    exit();
}

$id = $_GET['id'];
$conn->query("DELETE FROM blogs WHERE id=$id");
header("Location: blogs.php");
?>
