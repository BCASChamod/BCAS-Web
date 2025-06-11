<?php
require './config.php';
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ./view/login.html");
    exit();
}
$id = $_GET['id'];
$blog = $conn->query("SELECT * FROM blogs WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $author = $conn->real_escape_string($_POST['author']);
    $snippet = $conn->real_escape_string($_POST['snippet']);
    $date = $conn->real_escape_string($_POST['date']);

    $conn->query("UPDATE blogs SET title='$title', content='$content', author='$author', snippet='$snippet', date='$date' WHERE id=$id");
    header("Location: blogs.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow" />
    <title>Edit Blog</title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../stylesheets/editblog.css">
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Blog Editor</h1>
        <hr class="divider">
        <form method="POST" class="blog-form">
            <div class="formleft">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" class="form-input" value="<?= htmlspecialchars($blog['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" name="author" id="author" class="form-input" value="<?= htmlspecialchars($blog['author_id']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="snippet">Snippet:</label>
                    <textarea name="snippet" id="snippet" rows="4" class="form-textarea" required><?= htmlspecialchars($blog['snippet']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="text" name="date" id="date" class="form-input" value="<?= htmlspecialchars($blog['created_at']) ?>" required>
                </div>
            </div>
            <div class="formright">
                <div class="form-group">
                    <label for="editor">Content:</label>
                    <div id="editor" class="quill-editor"><?= htmlspecialchars($blog['content']) ?></div>
                    <input type="hidden" name="content" id="content">
                </div>
                <button type="submit" class="button primary-btn">Update</button>
            </div>
        </form>
        <hr class="divider">
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': '1' }, { 'header': '2' }, { 'font': [] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['bold', 'italic', 'underline'],
                    ['link'],
                    ['blockquote', 'code-block'],
                    ['image', 'video']
                ]
            }
        });

        var initialContent = `<?= addslashes($blog['content']) ?>`;
        quill.root.innerHTML = initialContent;

        document.querySelector('form').addEventListener('submit', function() {
            var content = quill.root.innerHTML;
            document.querySelector('#content').value = content;
        });
    </script>
    <script type="module" src="../../../dependencies/cfusion/cfusion.js"></script>
</body>
</html>
