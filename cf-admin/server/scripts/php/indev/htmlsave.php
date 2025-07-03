<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $file = $data['file'];
    $content = $data['content'];

    if (file_exists($file)) {
        file_put_contents($file, $content);
        echo "File updated successfully!";
    } else {
        echo "File not found!";
    }
}
?>
