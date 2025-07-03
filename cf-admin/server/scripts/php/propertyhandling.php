<?php 
require './config.php';

$sql = "SELECT id, `key`, values_json, created_at, updated_at FROM properties";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "No results found.";
}

?>