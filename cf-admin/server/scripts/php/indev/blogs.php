<?php
require './config.php';
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ./view/login.html");
    exit();
}

$result = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Dashboard</title>
    <meta name="robots" content="noindex, nofollow" />
    <link rel="stylesheet" href="../../../stylesheets/blogmanager.css">
</head>
<body>
    <div class="dashboard">
        <header class="dashboard-header">
            <h1>Manage Blogs</h1>
            <a href="../../../view/newblogs.html" class="button primary-btn"><button>Add New Blog</button></a>
        </header>
        <main class="dashboard-main">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="blog-item">
                    <div>
                        <h2><?= htmlspecialchars($row['title']) ?></h2>
                        <p><?= htmlspecialchars($row['snippet']) ?></p>
                    </div>
                    <div class="blog-actions">
                        <a href="./editblog.php?id=<?= $row['id'] ?>" class="button secondary-btn">Edit</a>
                        <a href="./deleteblog.php?id=<?= $row['id'] ?>" class="button danger-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
                <hr class="divider">
            <?php endwhile; ?>
        </main>
    </div>
    <script type="module" src="../../../dependencies/cfusion/cfusion.js"></script>
</body>
</html>
