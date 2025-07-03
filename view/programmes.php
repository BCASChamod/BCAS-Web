<?php
require '../cf-admin/server/scripts/php/config.php';

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

$fields = $conn->prepare("
    SELECT
        categories.id,
        categories.name
    FROM `categories`
    WHERE categories.is_active = 1
");
$fields->execute();
$fieldsResult = $fields->get_result();
$categories = $fieldsResult->fetch_all(MYSQLI_ASSOC);

$getproduct->execute();
$productsResult = $getproduct->get_result();

$products = $productsResult->fetch_all(MYSQLI_ASSOC);

?>
<script>
    window.categoriesAllData = <?php echo json_encode($categories); ?>;
    window.productAllData = <?php echo json_encode($products); ?>;
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programmes</title>
    <link rel="stylesheet" href="../stylesheets/global.css">
    <link rel="stylesheet" href="../stylesheets/programmes.css">
</head>
<body>
    
<div class="container-fluid main-wrapper">
    <!-- Mobile Toggle Button 
    <button class="mobile-toggle" id="mobileToggle">
        <i class="bi bi-funnel"></i>
    </button> -->

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="main-layout" style="margin: 160px 0px;">
        <main class="main-content">
            <div class="content-wrapper">
                <div class="page-header">
                    <h1>Programmes</h1>
                    <p class="page-description">
                        Discover our comprehensive range of academic programmes designed to shape your future career.
                    </p>
                    <div class="explore-all">
                        <span class="text-muted">Can't find what you're looking for?</span>
                        <a href="#" class="browse-link"><i style="margin-right: 5px;" class="fas fa-list"></i>Browse All Programmes</a>
                    </div>
                </div>

                <ul class="filter-tabs program-tabs">
                    <li class="filter-item">
                        <a class="filter-link active text-center" href="#undergraduate" data-tab="undergraduate">
                            <i class="fa-solid fa-graduation-cap me-2"></i> Undergraduate
                        </a>
                    </li>
                    <li class="filter-item">
                        <a class="filter-link text-center" href="#postgraduate" data-tab="postgraduate">
                            <i class="fa-solid fa-award me-2"></i> Postgraduate
                        </a>
                    </li>
                </ul>

                <div class="programs-grid" id="programsGrid" data-maxitem="10">
            
                <!-- Programs will be here -->

                </div>
            </div>
        </main>

        <aside class="sidebar" id="sidebar">
            <h5><i class="fa-solid fa-filter me-2"></i>Filter Programmes</h5>
            
            <div class="search-box">
                <i class="fa fa-search icon"></i>
                <input type="search" placeholder="Search programmes..." id="searchInput">
            </div>

            <div class="filter-section">
                <h6>Field </h6>
                <ul id="fieldList" class="filter-list">
                    <li data-filter="all">All Programmes</li>
                </ul>
            </div>

            <div class="filter-section">
                <h6>Duration</h6>
                <ul class="filter-list">
                    <li data-duration="all">Any Duration</li>
                    <li data-duration="3">3 Years</li>
                    <li data-duration="4">4 Years</li>
                    <li data-duration="5">5+ Years</li>
                </ul>
            </div>
        </aside>
    </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="../cf-admin/dependencies/cfusion/cfusion.js"></script>
<script src="../scripts/js/global.js"></script>
<script src="../scripts/js/programmes.js"></script>
</body>
</html>
