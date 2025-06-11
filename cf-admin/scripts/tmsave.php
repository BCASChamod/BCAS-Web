<?php
$data = file_get_contents("php://input");
$jsonData = json_decode($data, true);

if (json_last_error() === JSON_ERROR_NONE) {
    file_put_contents('data.json', json_encode($jsonData, JSON_PRETTY_PRINT));
    echo json_encode(["status" => "success", "message" => "Data saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data"]);
}
?>
