<?php
    require '../cf-admin/server/scripts/php/config.php';
    
    // Get URL parameters
    $programType = isset($_GET['type']) ? $_GET['type'] : '';
    $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs</title>
    <link rel="stylesheet" href="http://localhost/bcas-web/stylesheets/global.css">
</head>
<body>
    <main>
        <section style="margin-top: 8rem;">
            <!-- Program Type Selection -->
            <div>
                <h1><a href="programmes.php">Programs</a></h1>
            </div>

            <?php if (!$programType && !$categoryId): ?>
                <!-- Initial view with program type options -->
                <div style="margin-top: 2rem;">
                    <h2>Select Program Type:</h2>
                    <ul>
                        <li><a href="programmes.php?type=Undergraduate">Undergraduate Programs</a></li>
                        <li><a href="programmes.php?type=Postgraduate">Postgraduate Programs</a></li>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($programType && !$categoryId): ?>
                <!-- Show categories for selected program type -->
                <h2><?php echo htmlspecialchars($programType); ?> Categories</h2>
                <p><a href="programmes.php">&larr; Back to Program Types</a></p>
                <?php
                
                    $query = "
                        SELECT DISTINCT c.id AS category_id, c.name AS category_name
                        FROM categories c
                        INNER JOIN products p ON c.id = p.category_id
                        WHERE p.program_type = ?
                    ";
                    
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "s", $programType);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo '<ul>';
                        while ($row = mysqli_fetch_assoc($result)) {
                            $category_id = htmlspecialchars($row['category_id']);
                            $category_name = htmlspecialchars($row['category_name']);
                            echo "<li><a href='programmes.php?type=$programType&category_id=$category_id'>$category_name</a></li>";
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No categories found for ' . htmlspecialchars($programType) . ' programs.</p>';
                    }
                ?>
            <?php endif; ?>

            <?php if ($programType && $categoryId): ?>
                <!-- Show products for selected category and program type -->
                <?php
                    // Get category name first
                    $catQuery = "SELECT name FROM categories WHERE id = ?";
                    $catStmt = mysqli_prepare($conn, $catQuery);
                    mysqli_stmt_bind_param($catStmt, "i", $categoryId);
                    mysqli_stmt_execute($catStmt);
                    $catResult = mysqli_stmt_get_result($catStmt);
                    
                    if ($catResult && mysqli_num_rows($catResult) > 0) {
                        $catRow = mysqli_fetch_assoc($catResult);
                        $category_name = htmlspecialchars($catRow['name']);
                        echo "<h2>$category_name - $programType Programs</h2>";
                    } else {
                        echo "<h2>Programs</h2>";
                    }
                ?>
                <p><a href="programmes.php?type=<?php echo $programType; ?>">&larr; Back to Categories</a></p>
                <?php
                    $query = "
                        SELECT id, name
                        FROM products
                        WHERE category_id = ? AND program_type = ?
                    ";
                    
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "is", $categoryId, $programType);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo '<ul>';
                        while ($row = mysqli_fetch_assoc($result)) {
                            $product_id = htmlspecialchars($row['id']);
                            $product_name = htmlspecialchars($row['name']);
                            echo "<li><a href='product_detail.php?id=$product_id'>$product_name</a></li>";
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No programs found in this category.</p>';
                    }
                ?>
            <?php endif; ?>
        </section>
    </main>

    <!-- JS Files -->
    <script type="module" src="http://localhost/bcas-web/cf-admin/dependencies/cfusion/cfusion.js"></script>
    <script type="module" src="http://localhost/bcas-web/scripts/js/global.js"></script>
</body>
</html>