<?php
require './config.php';
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ./view/login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $author = $conn->real_escape_string($_POST['author']);
    $snippet = $conn->real_escape_string($_POST['snippet']);
    $pub_date = $conn->real_escape_string($_POST['pub_date']);
    $category = $conn->real_escape_string($_POST['category']);
    $affiliates = $conn->real_escape_string($_POST['affiliates']);
    $comment_id = $conn->real_escape_string($_POST['comment_id']);
    $meta = json_encode($_POST['meta']); // Assuming `meta` is a JSON array
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $date = date('Y-m-d H:i:s');

    $author_id = 1; // Replace with actual logic for fetching the author_id

    // Insert into database
    $conn->query("INSERT INTO blogs (title, content, author_id, snippet, pub_date, category, affiliates, comment_id, meta, is_active, created_at) 
                  VALUES ('$title', '$content', '$author_id', '$snippet', '$pub_date', '$category', '$affiliates', '$comment_id', '$meta', '$is_active', '$date')");
    header("Location: blogs.php");
}
?>
