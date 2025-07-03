<?php
require './config.php';

header('Content-Type: application/json');
$allowedOrigin = 'http://localhost/bcas-web/';
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowedOrigin) {
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
}
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === 'GET') {
    handleGetRequest($conn, $pdo);
}

if ($request_method === 'POST') {
    handlePostRequest($pdo);
}

#region READ Operations
function handleGetRequest($conn, $pdo) {
    try {
        // Get products with category and affiliation information
        $getproduct = $conn->prepare("
            SELECT 
                products.*,
                categories.name AS category_name,
                affiliations.title AS affiliation_title,
                affiliations.link AS affiliation_link,
                affiliations.logo_img AS affiliation_logo_img
            FROM `products`
            LEFT JOIN `categories` ON products.category_id = categories.id
            LEFT JOIN `affiliations` ON products.vendor = affiliations.id
            WHERE products.is_active = 1
        ");

        if (!$getproduct) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to prepare product query: ' . $conn->error]);
            exit;
        }

        // Get categories
        $fields = $conn->prepare("
            SELECT
                categories.id,
                categories.name
            FROM `categories`
            WHERE categories.is_active = 1
        ");

        if (!$fields) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to prepare categories query: ' . $conn->error]);
            exit;
        }

        // Execute categories query
        if (!$fields->execute()) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to execute categories query: ' . $fields->error]);
            exit;
        }

        $fieldsResult = $fields->get_result();
        if (!$fieldsResult) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get categories result: ' . $fields->error]);
            exit;
        }
        $categories = $fieldsResult->fetch_all(MYSQLI_ASSOC);

        // Execute products query
        if (!$getproduct->execute()) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to execute product query: ' . $getproduct->error]);
            exit;
        }

        $productsResult = $getproduct->get_result();
        if (!$productsResult) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get products result: ' . $getproduct->error]);
            exit;
        }
        $products = $productsResult->fetch_all(MYSQLI_ASSOC);

        // Get programs using PDO - all product fields
        $programsQuery = $pdo->prepare("
            SELECT * FROM products 
            WHERE is_active = 1 
            ORDER BY created_at DESC
        ");
        $programsQuery->execute();
        $programs = $programsQuery->fetchAll(PDO::FETCH_ASSOC);

        // Return combined data
        echo json_encode([
            'success' => true,
            'categories' => $categories,
            'products' => $products,
            'programs' => $programs
        ]);

    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
#endregion

function handlePostRequest($pdo) {
    if (!isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'Missing action parameter']);
        exit;
    }

    $action = $_POST['action'];

    #region DELETE Operation
    if ($action === 'delete') {
        handleDeleteAction($pdo);
        return;
    }
    #endregion

    #region CREATE/UPDATE Operations
    if ($action === 'create' || $action === 'update') {
        handleCreateUpdateAction($pdo, $action);
        return;
    }
    #endregion

    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

#region DELETE Operation
function handleDeleteAction($pdo) {
    if (!isset($_POST['program_id']) || empty($_POST['program_id'])) {
        echo json_encode(['success' => false, 'message' => 'Program ID is required for delete']);
        exit;
    }
    
    try {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $_POST['program_id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Program deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Program not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
#endregion

#region CREATE/UPDATE Operations
function handleCreateUpdateAction($pdo, $action) {
    if (!isset($_POST['program_data'])) {
        echo json_encode(['success' => false, 'message' => 'Missing program_data parameter']);
        exit;
    }

    $program_data = json_decode($_POST['program_data'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data: ' . json_last_error_msg()]);
        exit;
    }

    // Validate required fields
    if (!isset($program_data['name']) || trim($program_data['name']) === '') {
        echo json_encode(['success' => false, 'message' => 'Program name is required']);
        exit;
    }

    try {
        if ($action === 'create') {
            createProgram($pdo, $program_data);
        } elseif ($action === 'update') {
            updateProgram($pdo, $program_data);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
}

function createProgram($pdo, $program_data) {
    try {
        // Check what columns exist
        $checkColumns = $pdo->prepare("DESCRIBE products");
        $checkColumns->execute();
        $columns = $checkColumns->fetchAll(PDO::FETCH_COLUMN);
        
        $validColumns = [];
        $validValues = [];
        
        // Map all possible fields
        $fieldMapping = [
            'name' => $program_data['name'],
            'type' => $program_data['type'] ?? '',
            'price' => $program_data['price'] ?? 0,
            'duration' => $program_data['duration'] ?? 0,
            'level' => $program_data['level'] ?? '',
            'description' => $program_data['description'] ?? '',
            'pre_requirements' => $program_data['pre_requirements'] ?? '',
            'course_overview' => $program_data['course_overview'] ?? '',
            'program_structure' => $program_data['program_structure'] ?? '',
            'career_path' => $program_data['career_path'] ?? '',
            'student_guidance' => $program_data['student_guidance'] ?? '',
            'study_mode' => $program_data['study_mode'] ?? '',
            'branch_id' => isset($program_data['branch_ids']) ? implode(',', $program_data['branch_ids']) : '',
            'is_active' => $program_data['is_active'] ?? 1
        ];
        
        // Add only existing columns
        foreach ($fieldMapping as $field => $value) {
            if (in_array($field, $columns)) {
                $validColumns[] = $field;
                $validValues[":$field"] = $value;
            }
        }
        
        // Add timestamps if they exist
        if (in_array('created_at', $columns)) {
            $validColumns[] = 'created_at';
            $validValues[':created_at'] = date('Y-m-d H:i:s');
        }
        if (in_array('updated_at', $columns)) {
            $validColumns[] = 'updated_at';
            $validValues[':updated_at'] = date('Y-m-d H:i:s');
        }
        
        $sql = "INSERT INTO products (" . implode(', ', $validColumns) . ") VALUES (:" . implode(', :', $validColumns) . ")";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($validValues);
        
        $program_id = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Program created successfully',
            'program_id' => $program_id
        ]);
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function updateProgram($pdo, $program_data) {
    if (!isset($program_data['id']) || empty($program_data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Program ID is required for update']);
        exit;
    }
    
    try {
        // Check what columns exist
        $checkColumns = $pdo->prepare("DESCRIBE products");
        $checkColumns->execute();
        $columns = $checkColumns->fetchAll(PDO::FETCH_COLUMN);
        
        $validUpdates = [];
        $validValues = [];
        
        // Map all possible fields
        $fieldMapping = [
            'name' => $program_data['name'],
            'type' => $program_data['type'] ?? '',
            'price' => $program_data['price'] ?? 0,
            'duration' => $program_data['duration'] ?? 0,
            'level' => $program_data['level'] ?? '',
            'description' => $program_data['description'] ?? '',
            'pre_requirements' => $program_data['pre_requirements'] ?? '',
            'course_overview' => $program_data['course_overview'] ?? '',
            'program_structure' => $program_data['program_structure'] ?? '',
            'career_path' => $program_data['career_path'] ?? '',
            'student_guidance' => $program_data['student_guidance'] ?? '',
            'study_mode' => $program_data['study_mode'] ?? '',
            'branch_id' => isset($program_data['branch_ids']) ? implode(',', $program_data['branch_ids']) : '',
            'is_active' => $program_data['is_active'] ?? 1
        ];
        
        // Add only existing columns
        foreach ($fieldMapping as $field => $value) {
            if (in_array($field, $columns)) {
                $validUpdates[] = "$field = :$field";
                $validValues[":$field"] = $value;
            }
        }
        
        // Add updated_at if it exists
        if (in_array('updated_at', $columns)) {
            $validUpdates[] = 'updated_at = :updated_at';
            $validValues[':updated_at'] = date('Y-m-d H:i:s');
        }
        
        $validValues[':id'] = $program_data['id'];
        
        $sql = "UPDATE products SET " . implode(', ', $validUpdates) . " WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($validValues);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Program updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made or program not found']);
        }
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
#endregion

// Close database connections
$conn->close();
$pdo = null;
?>