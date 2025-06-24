<?php
require '../cf-admin/server/scripts/php/config.php';

// Get URL parameters
$programType = isset($_GET['type']) ? $_GET['type'] : 'Undergraduate';
$categoryFilter = isset($_GET['category']) ? [$_GET['category']] : [];
$levelFilter = isset($_GET['level']) ? [$_GET['level']] : [];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$isAjax = isset($_GET['ajax']);
$productsPerPage = 10;

// Find School of Business category ID
$businessCategoryId = null;
$categoriesQuery = "SELECT id, name FROM categories";
$categoriesResult = mysqli_query($conn, $categoriesQuery);
$allCategories = [];
if ($categoriesResult) {
    while ($row = mysqli_fetch_assoc($categoriesResult)) {
        $allCategories[] = $row;
        if ($row['name'] === 'School of Business') {
            $businessCategoryId = $row['id'];
        }
    }
}

// Set default category for Postgraduate if not already set
if ($programType === 'Postgraduate' && empty($categoryFilter) && $businessCategoryId) {
    $categoryFilter = [$businessCategoryId];
}

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
    
    $query = "SELECT p.id, p.name, p.level FROM products p 
              JOIN categories c ON p.category_id = c.id 
              WHERE p.program_type = ?";
    
    $params = [$programType];
    $types = 's';
    
    // Apply level filters
    if (!empty($levels)) {
        $levelConditions = [];
        foreach ($levels as $level) {
            if ($level === 'after_o') {
                // After O Level: Certificate, Foundation, Diploma
                $levelConditions[] = "(p.level = 'Certificate' OR p.level = 'Foundation' OR p.level = 'Diploma')";
            } elseif ($level === 'after_a') {
                // After A Level: Diploma, HND
                $levelConditions[] = "(p.level = 'Diploma' OR p.level = 'HND')";
            } elseif ($level === 'diploma') {
                // Diploma: Diploma, HND, BSc
                $levelConditions[] = "(p.level = 'Diploma' OR p.level = 'HND' OR p.level = 'BSc')";
            } elseif ($level === 'other') {
                if ($programType === 'Undergraduate') {
                    // Undergraduate Other: BSc
                    $levelConditions[] = "(p.level = 'BSc')";
                }
                // Postgraduate Other: No level filter (show all)
            }
        }
        
        if (!empty($levelConditions)) {
            $query .= " AND (" . implode(" OR ", $levelConditions) . ")";
        }
    }
    
    // Apply category filters
    if (!empty($categories)) {
        $query .= " AND p.category_id = ?";
        $params[] = $categories[0];
        $types .= 'i';
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
                $levelConditions[] = "(p.level = 'Certificate' OR p.level = 'Foundation' OR p.level = 'Diploma')";
            } elseif ($level === 'after_a') {
                $levelConditions[] = "(p.level = 'Diploma' OR p.level = 'HND')";
            } elseif ($level === 'diploma') {
                $levelConditions[] = "(p.level = 'Diploma' OR p.level = 'HND' OR p.level = 'BSc')";
            } elseif ($level === 'other') {
                if ($programType === 'Undergraduate') {
                    $levelConditions[] = "(p.level = 'BSc')";
                }
            }
        }
        
        if (!empty($levelConditions)) {
            $query .= " AND (" . implode(" OR ", $levelConditions) . ")";
        }
    }
    
    // Apply category filters
    if (!empty($categories)) {
        $query .= " AND p.category_id = ?";
        $params[] = $categories[0];
        $types .= 'i';
    }
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result->fetch_assoc();
    return $row['total'];
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
    <link rel="stylesheet" href="../stylesheets/programmes.css">
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
                            onclick="switchProgramType('Undergraduate')">
                            Undergraduate
                        </h1>
                        <h1 class="program-type <?= $programType === 'Postgraduate' ? 'active' : '' ?>" 
                            onclick="switchProgramType('Postgraduate')">
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
                    
                    <div class="filter-group">
                        <h3>Are you after?</h3>
                        <div class="filter-option <?= $programType === 'Postgraduate' ? 'disabled-option' : '' ?>">
                            <input type="radio" id="after_o" name="level" value="after_o" <?= in_array('after_o', $levelFilter) ? 'checked' : '' ?> <?= $programType === 'Postgraduate' ? 'disabled' : '' ?>>
                            <label for="after_o">O Level</label>
                        </div>
                        <div class="filter-option <?= $programType === 'Postgraduate' ? 'disabled-option' : '' ?>">
                            <input type="radio" id="after_a" name="level" value="after_a" <?= in_array('after_a', $levelFilter) ? 'checked' : '' ?> <?= $programType === 'Postgraduate' ? 'disabled' : '' ?>>
                            <label for="after_a">A Level</label>
                        </div>
                        <div class="filter-option <?= $programType === 'Postgraduate' ? 'disabled-option' : '' ?>">
                            <input type="radio" id="diploma" name="level" value="diploma" <?= in_array('diploma', $levelFilter) ? 'checked' : '' ?> <?= $programType === 'Postgraduate' ? 'disabled' : '' ?>>
                            <label for="diploma">Diploma</label>
                        </div>
                        <div class="filter-option <?= $programType === 'Postgraduate' ? 'forced-selection' : '' ?>">
                            <input type="radio" id="other" name="level" value="other" <?= in_array('other', $levelFilter) || $programType === 'Postgraduate' ? 'checked' : '' ?>>
                            <label for="other">Other</label>
                            <?php if ($programType === 'Postgraduate'): ?>
                                <span style="font-size: 0.8em; color: #666;">(Required for Postgraduate)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h3>Select Field Of Study</h3>
                        <?php foreach ($allCategories as $category): ?>
                            <?php
                            $isDefault = ($programType === 'Postgraduate' && 
                                          $category['id'] == $businessCategoryId && 
                                          empty($categoryFilter));
                            ?>
                            <div class="filter-option <?= $isDefault ? 'default-category' : '' ?>">
                                <input type="radio" id="cat_<?= $category['id'] ?>" name="category" value="<?= $category['id'] ?>" 
                                    <?= in_array($category['id'], $categoryFilter) ? 'checked' : '' ?>
                                    <?= $isDefault ? 'checked' : '' ?>>
                                <label for="cat_<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></label>
                                <?php if ($isDefault): ?>
                                    <span style="font-size: 0.8em; color: #666;">(Default)</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="button" onclick="clearFilters()">Clear Filters</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Store business category ID for JavaScript use
        const businessCategoryId = <?= $businessCategoryId ? json_encode($businessCategoryId) : 'null' ?>;
        let currentProgramType = "<?= $programType ?>";
        
        // Function to switch program types and reset filters
        function switchProgramType(type) {
            // Update UI
            document.querySelectorAll('.program-type').forEach(el => {
                el.classList.remove('active');
            });
            
            if (type === 'Undergraduate') {
                document.querySelector('.program-type:first-child').classList.add('active');
                currentProgramType = 'Undergraduate';
            } else if (type === 'Postgraduate') {
                document.querySelector('.program-type:nth-child(2)').classList.add('active');
                currentProgramType = 'Postgraduate';
            }
            
            // Update hidden field
            document.getElementById('filter-type').value = type;
            
            // Reset filters
            clearFilters();
        }

        // Function to dim non-selected options in a group
        function dimNonSelectedOptions(groupName) {
            const options = document.querySelectorAll(`.filter-option input[name="${groupName}"]:not(:disabled)`);
            let selectedValue = null;
            
            // Find the selected value
            options.forEach(option => {
                if (option.checked) {
                    selectedValue = option.value;
                }
            });
            
            // Apply dimming only to non-disabled options
            options.forEach(option => {
                const container = option.closest('.filter-option');
                if (selectedValue !== null && option.value !== selectedValue) {
                    container.classList.add('dimmed');
                } else {
                    container.classList.remove('dimmed');
                }
            });
        }
        
        // Function to update dimming for all groups
        function updateAllDimming() {
            dimNonSelectedOptions('level');
            dimNonSelectedOptions('category');
        }
        
        // Function to update UI based on program type
        function updateUIForProgramType() {
            const programType = document.getElementById('filter-type').value;
            const levelOptions = document.querySelectorAll('.filter-option input[name="level"]');
            
            levelOptions.forEach(option => {
                const container = option.closest('.filter-option');
                
                if (programType === 'Postgraduate') {
                    if (option.value !== 'other') {
                        option.disabled = true;
                        container.classList.add('disabled-option');
                    } else {
                        option.checked = true;
                        container.classList.remove('disabled-option');
                    }
                } else {
                    option.disabled = false;
                    container.classList.remove('disabled-option');
                }
            });
        }

        // Function to clear filters
        function clearFilters() {
            // Uncheck all radio buttons
            document.querySelectorAll('input[type="radio"]:not([name="level"][value="other"]):not([disabled])').forEach(radio => {
                radio.checked = false;
            });
            
            // For Postgraduate, set defaults
            const programType = document.getElementById('filter-type').value;
            if (programType === 'Postgraduate') {
                // Set level to "other"
                document.querySelector('input[name="level"][value="other"]').checked = true;
                
                // Set default category to School of Business
                if (businessCategoryId) {
                    const categoryRadio = document.querySelector(`input[name="category"][value="${businessCategoryId}"]`);
                    if (categoryRadio) {
                        categoryRadio.checked = true;
                    }
                }
            }
            
            updateUIForProgramType();
            updateAllDimming();
            
            loadCourseList();
        }

        function loadCourseList(page = 1) {
            const form = document.getElementById('filter-form');
            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            // Add form data to params
            for (const [name, value] of formData.entries()) {
                params.set(name, value);
            }
            
            if (params.get('type') === 'Postgraduate') {
                params.set('level', 'other');
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
                    // Update dimming after content loads
                    updateAllDimming();
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
            const level = params.get('level');
            const category = params.get('category');
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
            
            // Update radio buttons
            if (level) {
                document.querySelector(`input[name="level"][value="${level}"]`).checked = true;
            } else if (type === 'Postgraduate') {
                document.querySelector(`input[name="level"][value="other"]`).checked = true;
            } else {
                document.querySelectorAll('input[name="level"]').forEach(radio => {
                    if (!radio.disabled) radio.checked = false;
                });
            }
            
            if (category) {
                document.querySelector(`input[name="category"][value="${category}"]`).checked = true;
            } else {
                document.querySelectorAll('input[name="category"]').forEach(radio => {
                    radio.checked = false;
                });
            }
            
            // Update UI
            updateUIForProgramType();
            updateAllDimming();
            
            // Reload course list
            loadCourseList(page);
        });

        // Add event listeners for live filtering
        document.addEventListener('DOMContentLoaded', function() {
            // Initial setup
            updateUIForProgramType();
            updateAllDimming();
            
            // Listen to level filter changes
            document.querySelectorAll('input[name="level"]:not(:disabled)').forEach(radio => {
                radio.addEventListener('change', () => {
                    // Update dimming immediately
                    dimNonSelectedOptions('level');
                    loadCourseList();
                });
            });
            
            // Listen to category filter changes
            document.querySelectorAll('input[name="category"]').forEach(radio => {
                radio.addEventListener('change', () => {
                    // Update dimming immediately
                    dimNonSelectedOptions('category');
                    loadCourseList();
                });
            });
        });
    </script>
    <!-- <script type="module" src="../cf-admin/dependencies/cfusion/cfusion.js"></script> -->
</body>
</html>
