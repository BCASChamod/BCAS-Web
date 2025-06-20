<?php
    require '../cf-admin/server/scripts/php/config.php';
    
    // Get URL parameters
    $programType = isset($_GET['type']) ? $_GET['type'] : '';
    $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : '';
    $isAjax = isset($_GET['ajax']);
    $featuredPage = isset($_GET['featured_page']) ? (int)$_GET['featured_page'] : 1;
    $productsPerPage = 10;
    
    // [Keep all your existing functions here - getCategories, getProducts, getCategoryName]
    
    // Handle AJAX requests
    if ($isAjax) {
        if (isset($_GET['featured'])) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $productsPerPage;
            
            // Get total count of products
            $countQuery = "SELECT COUNT(*) as total FROM products";
            $countResult = mysqli_query($conn, $countQuery);
            $totalProducts = mysqli_fetch_assoc($countResult)['total'];
            $totalPages = ceil($totalProducts / $productsPerPage);
            
            // Get paginated products
            $featuredQuery = "SELECT id, name FROM products LIMIT $offset, $productsPerPage";
            $featuredResult = mysqli_query($conn, $featuredQuery);
            
            echo '<div>
                <h2>Featured Programs</h2>
                <div>
                    <ul>';
                        if ($featuredResult && mysqli_num_rows($featuredResult) > 0) {
                            while ($row = mysqli_fetch_assoc($featuredResult)) {
                                echo "<li><a href='product_detail.php?id=".htmlspecialchars($row['id'])."'>".htmlspecialchars($row['name'])."</a></li>";
                            }
                        } else {
                            echo '<p>No featured programs found.</p>';
                        }
            echo '    </ul>
                </div>
                <div class="pagination">';
                    if ($page > 1) {
                        echo '<button onclick="loadFeaturedPage('.($page - 1).')">Previous</button>';
                    }
                    if ($page < $totalPages) {
                        echo '<button onclick="loadFeaturedPage('.($page + 1).')">Next</button>';
                    }
            echo '  </div>
            </div>
            <div>
                <h2>Browse Categories</h2>
                <div>
                    <ul>';
                        $categoriesQuery = "SELECT id, name FROM categories";
                        $categoriesResult = mysqli_query($conn, $categoriesQuery);
                        if ($categoriesResult && mysqli_num_rows($categoriesResult) > 0) {
                            while ($row = mysqli_fetch_assoc($categoriesResult)) {
                                echo "<li><span onclick=\"loadContent('category_id=".htmlspecialchars($row['id'])."')\">".htmlspecialchars($row['name'])."</span></li>";
                            }
                        } else {
                            echo '<p>No categories found.</p>';
                        }
            echo '    </ul>
                </div>
            </div>';
            exit;
        }
        // [Keep your existing AJAX handling code for categories and products]
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs</title>
    <link rel="stylesheet" href="http://localhost/bcas-web/stylesheets/global.css">
    <style>
        .pagination {
            margin-top: 1rem;
            display: flex;
            gap: 1rem;
        }
        .pagination button {
            padding: 0.5rem 1rem;
            cursor: pointer;
        }
    </style>
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

            <!-- Main Content Area -->
            <div id="main-content">
                <?php if (!$programType && !$categoryId): ?>
                    <!-- Featured Programs and Categories Section -->
                    <section id="featured-section">
                        <!-- Content loaded via AJAX -->
                    </section>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        // Function to load featured content with pagination
        function loadFeaturedPage(page) {
            fetch(`programmes.php?ajax=1&featured=1&page=${page}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('featured-section').innerHTML = data;
                    // Update URL without reload
                    history.pushState(null, null, `programmes.php?featured_page=${page}`);
                });
        }
        
        // Function to update URL and load content
        function loadContent(params) {
            // Update browser URL without reload
            const newUrl = params ? `programmes.php?${params}` : 'programmes.php';
            history.pushState(null, null, newUrl);
            
            // Parse parameters
            const urlParams = new URLSearchParams(params);
            const type = urlParams.get('type');
            const categoryId = urlParams.get('category_id');
            
            if (!type && !categoryId) {
                // Show featured section when back to home
                document.getElementById('main-content').innerHTML = `
                    <section id="featured-section">
                        <!-- Featured content will be reloaded from server -->
                    </section>
                `;
                loadFeaturedPage(1);
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
        
        // [Keep your existing fetchCategories, fetchProducts, fetchCategoryProducts functions]
        
        // Handle back/forward browser buttons
        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const featuredPage = urlParams.get('featured_page');
            
            if (featuredPage) {
                loadFeaturedPage(parseInt(featuredPage));
            } else {
                loadContent(window.location.search.substring(1));
            }
        });
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type');
            const categoryId = urlParams.get('category_id');
            const featuredPage = urlParams.get('featured_page');
            
            if (featuredPage) {
                loadFeaturedPage(parseInt(featuredPage));
            } else if (type || categoryId) {
                loadContent(window.location.search.substring(1));
            } else {
                loadFeaturedPage(1);
            }
        });
    </script>
</body>
</html>