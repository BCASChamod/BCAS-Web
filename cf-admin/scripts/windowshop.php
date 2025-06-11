<?php
require 'db.php';

$keyName = 'window_shop';
$sql = "SELECT value FROM user_configs WHERE key_name = '$keyName' AND is_active = TRUE";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $courses = json_decode($row['value'], true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($courses)) {
        $courseIds = array_map(function($course) {
            return $course['course_id'];
        }, $courses);

        $courseIdsStr = implode(',', $courseIds);

        $sqlProducts = "SELECT id, name, description, image_id FROM products WHERE id IN ($courseIdsStr) AND is_active = TRUE";
        $productResult = $conn->query($sqlProducts);

        if ($productResult->num_rows > 0) {
            while ($product = $productResult->fetch_assoc()) {
                $products[] = $product;
            }
        }
    }
}

if (empty($products)) {
    $sqlFallback = "SELECT id, name, description, image_id 
                    FROM products 
                    WHERE is_active = TRUE 
                    ORDER BY created_at DESC 
                    LIMIT 3";
    $fallbackResult = $conn->query($sqlFallback);

    if ($fallbackResult->num_rows > 0) {
        while ($product = $fallbackResult->fetch_assoc()) {
            $products[] = $product;
        }
    }
}

echo json_encode($products);

$conn->close();
?>
