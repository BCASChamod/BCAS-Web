<?php
require './config.php';

$sql = "SELECT id, type, element_id, src, alt, srcset, name, is_active FROM medialibrary"; 
$result = $conn->query($sql);

$mediaData = array();
$logMessages = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $mediaData[$row["element_id"]] = $row; 
        if (!$row["is_active"]) {
            $logMessages[] = array(
                "message" => "Element ID " . $row["element_id"] . " is not loaded with an image cause its disabled! Re-enable it or change it.",
                "log_type" => "Warning",
                "priority" => 1,
                "affected_items" => $row["element_id"]
            );
        }
    }
}

foreach ($logMessages as $logMessage) {
    $logSql = "INSERT INTO log (message, log_type, priority, affected_items) VALUES ('{$logMessage['message']}', '{$logMessage['log_type']}', '{$logMessage['priority']}', '{$logMessage['affected_items']}')";
    $conn->query($logSql);
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($mediaData); 
?>
