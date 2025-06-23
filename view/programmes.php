<?php
require '../cf-admin/server/scripts/php/config.php';

// Get URL parameters
$programType = isset($_GET['type']) ? $_GET['type'] : 'Undergraduate';
$categoryFilter = isset($_GET['categories']) ? (array)$_GET['categories'] : [];
$levelFilter = isset($_GET['levels']) ? (array)$_GET['levels'] : [];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$isAjax = isset($_GET['ajax']);
$productsPerPage = 10;

// Define level order for sorting
$levelOrder = [
    'Certificate' => 1,
    'Foundation' => 2,
    'Diploma' => 3,
    'HND' => 4,
    'BSc' => 5,
    'Msc' => 6,
    'Pgdip' => 7
];

// Function to get filtered products with sorting
function getFilteredProducts($conn, $programType, $categories, $levels, $page, $perPage, $levelOrder) {
    $offset = ($page - 1) * $perPage;
    
    $query = "SELECT p.id, p.name FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.program_type = ?";
    
    $params = [$programType];
    $types = 's';
    
    // Apply level filters
    if (!empty($levels)) {
        $levelConditions = [];
        foreach ($levels as $level) {
            if ($level === 'after_o') {
                $levelConditions[] = "(p.level = 'Certificate' OR p.level = 'Foundation')";
            } elseif ($level === 'after_a') {
                $levelConditions[] = "(p.level = 'HND' OR p.level = 'Diploma')";
            } elseif ($level === 'diploma') {
                $levelConditions[] = "(p.level = 'HND' OR p.level = 'Diploma')";
            } elseif ($level === 'other') {
                // Show all levels
            }
        }
        
        if (!empty($levelConditions)) {
            $query .= " AND (" . implode(" OR ", $levelConditions) . ")";
        }
    }
    
    // Apply category filters
    if (!empty($categories)) {
        $placeholders = implode(',', array_fill(0, count($categories), '?'));
        $query .= " AND p.category_id IN ($placeholders)";
        $params = array_merge($params, $categories);
        $types .= str_repeat('i', count($categories));
    }
    
    // Add sorting by level
    $query .= " ORDER BY CASE p.level ";
    foreach ($levelOrder as $level => $order) {
        $query .= "WHEN '$level' THEN $order ";
    }
    $query .= "ELSE 99 END"; // Default for other levels
    
    // Add pagination
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Function to get total count of filtered products
function getTotalFilteredProducts($conn, $programType, $categories, $levels) {
    $query = "SELECT COUNT(*) as total FROM products p WHERE p.program_type = ?";
    $params = [$programType];
    $types = 's';
    
    // Apply level filters
    if (!empty($levels)) {
        $levelConditions = [];
        foreach ($levels as $level) {
            if ($level === 'after_o') {
                $levelConditions[] = "(p.level = 'Certificate' OR p.level = 'Foundation')";
            } elseif ($level === 'after_a') {
                $levelConditions[] = "(p.level = 'HND' OR p.level = 'Diploma')";
            } elseif ($level === 'diploma') {
                $levelConditions[] = "(p.level = 'HND' OR p.level = 'Diploma')";
            } elseif ($level === 'other') {
                // Show all levels
            }
        }
        
        if (!empty($levelConditions)) {
            $query .= " AND (" . implode(" OR ", $levelConditions) . ")";
        }
    }
    
    // Apply category filters
    if (!empty($categories)) {
        $placeholders = implode(',', array_fill(0, count($categories), '?'));
        $query .= " AND p.category_id IN ($placeholders)";
        $params = array_merge($params, $categories);
        $types .= str_repeat('i', count($categories));
    }
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Get all categories for the filter form
$categoriesQuery = "SELECT id, name FROM categories";
$categoriesResult = mysqli_query($conn, $categoriesQuery);
$allCategories = [];
if ($categoriesResult) {
    while ($row = mysqli_fetch_assoc($categoriesResult)) {
        $allCategories[] = $row;
    }
}

// Handle AJAX request for course list
if ($isAjax) {
    // Get filtered products
    $filteredProducts = getFilteredProducts($conn, $programType, $categoryFilter, $levelFilter, $page, $productsPerPage, $levelOrder);
    $totalProducts = getTotalFilteredProducts($conn, $programType, $categoryFilter, $levelFilter);
    $totalPages = ceil($totalProducts / $productsPerPage);
    
    // Output the course list HTML
    if ($filteredProducts && mysqli_num_rows($filteredProducts) > 0) {
        echo '<ul>';
        while ($product = mysqli_fetch_assoc($filteredProducts)) {
            // Only show product name as clickable link
            echo '<li><a href="product_detail.php?id=' . $product['id'] . '">' . 
                 htmlspecialchars($product['name']) . '</a></li>';
        }
        echo '</ul>';
        
        // Pagination
        echo '<div>';
        if ($page > 1) {
            echo '<button onclick="handlePagination(' . ($page - 1) . ')">Previous</button>';
        }
        if ($page < $totalPages) {
            echo '<button onclick="handlePagination(' . ($page + 1) . ')">Next</button>';
        }
        echo '</div>';
    } else {
        echo '<p>No programs found matching your criteria.</p>';
    }
    exit;
}

// For initial page load, get products without AJAX
$filteredProducts = getFilteredProducts($conn, $programType, $categoryFilter, $levelFilter, $page, $productsPerPage, $levelOrder);
$totalProducts = getTotalFilteredProducts($conn, $programType, $categoryFilter, $levelFilter);
$totalPages = ceil($totalProducts / $productsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .program-type {
            cursor: pointer;
            margin-bottom: 10px;
            color: #0066cc;
            text-decoration: underline;
        }
        .program-type.active {
            font-weight: bold;
        }
        #course_filter ul {
            list-style-type: none;
            padding-left: 0;
        }
        #course_filter li {
            margin-bottom: 10px;
        }
        #course_filter a {
            text-decoration: none;
            color: #333;
        }
        #course_filter a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Program Type Selection -->
                <div class="row">
                    <div class="col">
                        <h1 class="program-type <?= $programType === 'Undergraduate' ? 'active' : '' ?>" 
                            onclick="loadContent('type=Undergraduate')">
                            Undergraduate
                        </h1>
                        <h1 class="program-type <?= $programType === 'Postgraduate' ? 'active' : '' ?>" 
                            onclick="loadContent('type=Postgraduate')">
                            Postgraduate
                        </h1>
                    </div>
                </div>
                
                <!-- Course Filter Results -->
                <div class="row" id="course_filter">
                    <div class="col">
                        <h2><?= htmlspecialchars($programType) ?> Programs</h2>
                        <?php if ($filteredProducts && mysqli_num_rows($filteredProducts) > 0): ?>
                            <ul>
                                <?php while ($product = mysqli_fetch_assoc($filteredProducts)): ?>
                                    <!-- Only show product name as clickable link -->
                                    <li><a href="product_detail.php?id=<?= $product['id'] ?>">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a></li>
                                <?php endwhile; ?>
                            </ul>
                            
                            <!-- Pagination -->
                            <div>
                                <?php if ($page > 1): ?>
                                    <button onclick="handlePagination(<?= $page - 1 ?>)">Previous</button>
                                <?php endif; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <button onclick="handlePagination(<?= $page + 1 ?>)">Next</button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p>No programs found matching your criteria.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Filter Form -->
            <div class="col-md-4">
                <form id="filter-form">
                    <input type="hidden" name="type" id="filter-type" value="<?= $programType ?>">
                    
                    <h3>Are you after?</h3>
                    <div>
                        <input type="checkbox" id="after_o" name="levels[]" value="after_o" <?= in_array('after_o', $levelFilter) ? 'checked' : '' ?>>
                        <label for="after_o">O Level</label>
                    </div>
                    <div>
                        <input type="checkbox" id="after_a" name="levels[]" value="after_a" <?= in_array('after_a', $levelFilter) ? 'checked' : '' ?>>
                        <label for="after_a">A Level</label>
                    </div>
                    <div>
                        <input type="checkbox" id="diploma" name="levels[]" value="diploma" <?= in_array('diploma', $levelFilter) ? 'checked' : '' ?>>
                        <label for="diploma">Diploma</label>
                    </div>
                    <div>
                        <input type="checkbox" id="other" name="levels[]" value="other" <?= in_array('other', $levelFilter) ? 'checked' : '' ?>>
                        <label for="other">Other</label>
                    </div>
                    
                    <h3>Select Field Of Study</h3>
                    <?php foreach ($allCategories as $category): ?>
                        <div>
                            <input type="checkbox" id="cat_<?= $category['id'] ?>" name="categories[]" value="<?= $category['id'] ?>" <?= in_array($category['id'], $categoryFilter) ? 'checked' : '' ?>>
                            <label for="cat_<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></label>
                        </div>
                    <?php endforeach; ?>
                    
                    <button type="button" onclick="applyFilters()">Submit</button>
                    <button type="button" onclick="clearFilters()">Clear Filters</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Function to load program type
        function loadContent(params) {
            // Update URL without reload
            const urlParams = new URLSearchParams(params);
            const type = urlParams.get('type');
            
            // Update UI
            document.querySelectorAll('.program-type').forEach(el => {
                el.classList.remove('active');
            });
            
            if (type === 'Undergraduate') {
                document.querySelector('.program-type:first-child').classList.add('active');
            } else if (type === 'Postgraduate') {
                document.querySelector('.program-type:nth-child(2)').classList.add('active');
            }
            
            // Update hidden field
            document.getElementById('filter-type').value = type;
            
            // Load courses with current filters
            loadCourseList();
        }

        // Function to apply filters
        function applyFilters() {
            loadCourseList();
        }

        // Function to clear filters
        function clearFilters() {
            // Uncheck all checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reload with cleared filters
            loadCourseList();
        }

        // Function to load course list with current filters
        function loadCourseList(page = 1) {
            const form = document.getElementById('filter-form');
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            // Add form data to params
            for (const [name, value] of formData.entries()) {
                if (name === 'levels[]' || name === 'categories[]') {
                    params.append(name, value);
                } else {
                    params.set(name, value);
                }
            }
            
            // Add pagination and AJAX flag
            params.set('page', page);
            params.set('ajax', '1');
            
            // Update URL without reloading page
            history.pushState(null, '', '?' + params.toString());
            
            // Fetch course list
            fetch('?' + params.toString())
                .then(response => response.text())
                .then(data => {
                    document.getElementById('course_filter').innerHTML = data;
                });
        }

        // Handle pagination clicks
        function handlePagination(page) {
            loadCourseList(page);
        }

        // Handle browser back/forward
        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const type = params.get('type');
            const page = params.get('page') || 1;
            
            // Update UI
            if (type) {
                document.querySelectorAll('.program-type').forEach(el => {
                    el.classList.remove('active');
                });
                if (type === 'Undergraduate') {
                    document.querySelector('.program-type:first-child').classList.add('active');
                } else if (type === 'Postgraduate') {
                    document.querySelector('.program-type:nth-child(2)').classList.add('active');
                }
                document.getElementById('filter-type').value = type;
            }
            
            // Update checkboxes
            const levels = params.getAll('levels[]');
            const categories = params.getAll('categories[]');
            
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                if (checkbox.name === 'levels[]') {
                    checkbox.checked = levels.includes(checkbox.value);
                } else if (checkbox.name === 'categories[]') {
                    checkbox.checked = categories.includes(checkbox.value);
                }
            });
            
            // Reload course list
            loadCourseList(page);
        });
    </script>
</body>
</html>