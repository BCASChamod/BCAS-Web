<?php
require 'db.php';

$courses = $conn->query("
    SELECT 
        products.id, 
        products.name, 
        products.description, 
        products.category_id, 
        products.image_id,
        categories.name AS category_name
    FROM products 
    LEFT JOIN categories ON products.category_id = categories.id 
    WHERE products.is_active = 1
");

$coursesArray = array();

while ($row = $courses->fetch_assoc()) {
    $coursesArray[] = $row;
}

header('Content-Type: application/json');
echo json_encode($coursesArray);
?>
