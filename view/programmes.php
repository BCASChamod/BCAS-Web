<?php
    require '../cf-admin/server/scripts/php/config.php';
    
    // Get URL parameters
    $programType = isset($_GET['type']) ? $_GET['type'] : '';
    $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : '';
    $isAjax = isset($_GET['ajax']);
    
    // Function to fetch categories for a program type
    function getCategories($conn, $programType) {
        $query = "
            SELECT DISTINCT c.id AS category_id, c.name AS category_name
            FROM categories c
            INNER JOIN products p ON c.id = p.category_id
            WHERE p.program_type = ?
        ";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $programType);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
    
    // Function to fetch products for a category and program type
    function getProducts($conn, $categoryId, $programType) {
        $query = "
            SELECT id, name
            FROM products
            WHERE category_id = ? AND program_type = ?
        ";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "is", $categoryId, $programType);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }
    
    // Function to get category name
    function getCategoryName($conn, $categoryId) {
        $catQuery = "SELECT name FROM categories WHERE id = ?";
        $catStmt = mysqli_prepare($conn, $catQuery);
        mysqli_stmt_bind_param($catStmt, "i", $categoryId);
        mysqli_stmt_execute($catStmt);
        $result = mysqli_stmt_get_result($catStmt);
        return ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['name'] : '';
    }
    
    // Handle AJAX requests
    if ($isAjax) {
        if ($programType && !$categoryId) {
            // Return categories HTML
            $result = getCategories($conn, $programType);
            
            echo "<h2>" . htmlspecialchars($programType) . " Categories</h2>";
            echo "<span onclick=\"loadContent('')\">&larr; Back to Program Types</span>";
            echo "<div id='categories-list'>";
            
            if ($result && mysqli_num_rows($result) > 0) {
                echo '<ul>';
                while ($row = mysqli_fetch_assoc($result)) {
                    $category_id = htmlspecialchars($row['category_id']);
                    $category_name = htmlspecialchars($row['category_name']);
                    echo "<li><span onclick=\"loadContent('type=$programType&category_id=$category_id')\">$category_name</span></li>";
                }
                echo '</ul>';
            } else {
                echo '<p>No categories found for ' . htmlspecialchars($programType) . ' programs.</p>';
            }
            
            echo "</div>";
            exit;
        } elseif ($programType && $categoryId) {
            // Return products HTML
            $category_name = getCategoryName($conn, $categoryId);
            $result = getProducts($conn, $categoryId, $programType);
            
            echo "<h2>" . htmlspecialchars($category_name) . " - " . htmlspecialchars($programType) . " Programs</h2>";
            echo "<span onclick=\"loadContent('type=$programType')\">&larr; Back to Categories</span>";
            echo "<div id='products-list'>";
            
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
            
            echo "</div>";
            exit;
        }
    }
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
        <section>
            <div>
                <h1>Programs</h1>
            </div>
            
            <!-- Program Type Selection -->
            <div id="program-types">
                <h1 onclick="loadContent('type=Undergraduate')">Undergraduate</h1>
                <h1 onclick="loadContent('type=Postgraduate')">Postgraduate</h1>
            </div>

            <!-- Main Content Area (initially blank) -->
            <div id="main-content"></div>
        </section>
    </main>

    <!-- Featured Programs and Categories Section (shown by default) -->
    <section id="featured-section">
        <div>
            <!-- Featured Programs -->
            <div>
                <h2>Featured Programs</h2>
                <div>
                    <ul>
                        <?php
                        $featuredQuery = "SELECT id, name FROM products ORDER BY RAND() LIMIT 10";
                        $featuredResult = mysqli_query($conn, $featuredQuery);
                        if ($featuredResult && mysqli_num_rows($featuredResult) > 0) {
                            while ($row = mysqli_fetch_assoc($featuredResult)) {
                                echo "<li><a href='product_detail.php?id=".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</a></li>";
                            }
                        } else {
                            echo '<p>No featured programs found.</p>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            
            <!-- Categories List -->
            <div>
                <h2>Browse Categories</h2>
                <div>
                    <ul>
                        <?php
                        $categoriesQuery = "SELECT id, name FROM categories";
                        $categoriesResult = mysqli_query($conn, $categoriesQuery);
                        if ($categoriesResult && mysqli_num_rows($categoriesResult) > 0) {
                            while ($row = mysqli_fetch_assoc($categoriesResult)) {
                                echo "<li><span onclick=\"loadContent('category_id=".htmlspecialchars($row['id'])."')\">".htmlspecialchars($row['name'])."</span></li>";
                            }
                        } else {
                            echo '<p>No categories found.</p>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Function to update URL and load content
        function loadContent(params) {
            // Hide featured section when navigating
            document.getElementById('featured-section').style.display = 'none';
            
            // Update browser URL without reload
            const newUrl = params ? `programmes.php?${params}` : 'programmes.php';
            history.pushState(null, null, newUrl);
            
            // Parse parameters
            const urlParams = new URLSearchParams(params);
            const type = urlParams.get('type');
            const categoryId = urlParams.get('category_id');
            
            if (!type && !categoryId) {
                // Show program types and featured section
                document.getElementById('featured-section').style.display = 'block';
                document.getElementById('main-content').innerHTML = '';
            } else if (type && !categoryId) {
                // Fetch categories via AJAX
                fetchCategories(type);
            } else if (type && categoryId) {
                // Fetch products via AJAX
                fetchProducts(type, categoryId);
            } else if (categoryId && !type) {
                // Handle direct category clicks from featured section
                fetchCategoryProducts(categoryId);
            }
        }
        
        // Fetch categories via AJAX
        function fetchCategories(programType) {
            fetch(`programmes.php?ajax=1&type=${programType}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('main-content').innerHTML = data;
                });
        }
        
        // Fetch products via AJAX
        function fetchProducts(programType, categoryId) {
            fetch(`programmes.php?ajax=1&type=${programType}&category_id=${categoryId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('main-content').innerHTML = data;
                });
        }
        
        // Fetch products for category (from featured section)
        function fetchCategoryProducts(categoryId) {
            // First determine program type (could be either)
            fetch(`programmes.php?ajax=1&category_id=${categoryId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('main-content').innerHTML = data;
                });
        }
        
        // Handle back/forward browser buttons
        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type');
            const categoryId = urlParams.get('category_id');
            
            if (!type && !categoryId) {
                // Show featured section when back to home
                document.getElementById('featured-section').style.display = 'block';
                document.getElementById('main-content').innerHTML = '';
            } else {
                loadContent(window.location.search.substring(1));
            }
        });
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type');
            const categoryId = urlParams.get('category_id');
            
            if (type || categoryId) {
                loadContent(window.location.search.substring(1));
            }
        });
    </script>
</body>
</html>