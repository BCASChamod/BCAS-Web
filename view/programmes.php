<?php
require '../cf-admin/server/scripts/php/config.php';

$getproduct = $conn->prepare("SELECT products.*, categories.name AS category_name FROM `products` LEFT JOIN `categories` ON products.category_id = categories.id WHERE products.is_active = 1");
$getproduct->execute();

$productsResult = $getproduct->get_result();

$products = $productsResult->fetch_all(MYSQLI_ASSOC);

?>
<script>
    window.productAllData = <?php echo json_encode($products); ?>;
    console.log(window.productAllData);
</script>

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
            <div class="col-md-8">
                <div class="row">
                    <h1 class="ugBtn" id="ugbtn" name="ugbtn">UNDERGRADUATE</h1>
                    <h1 class="pgBtn" id="pgbtn" name="pgbtn">POSTGRADUATE</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row programmes area">
                        <div class="programmes-header">
                            <h2>Programmes</h2>
                        </div>
                        <div class="row selected_headings">
                            <h2 class="ugpro">Undargraduate Programs</h2>
                            <h2 class="pgpro">Postgraduate Programs</h2>
                        </div>
                        <div class="row program_result">
                            <div id="programContainer">
                                <!-- Programmes will be here -->
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row level_selection">
                    <div class="row">
                        <h2 class="level_heading">Are you after?</h2>
                    </div>
                    <div class="row level_selection_form"> 
                        <form method="post">
                            <label><input type="radio" name="olevel" value="olevel"> O Level</label><br>
                            <label><input type="radio" name="alevel" value="alevel"> A Level</label><br>
                            <label><input type="radio" name="diploma" value="diploma"> Diploma</label><br>
                            <label><input type="radio" name="other" value="other"> Other</label><br>
                        </form>
                    </div>
                </div>
                <div class="row cateogory_selection">
                    <div class="row">
                        <h2 class="category_selection_heading">Interested Field Of Study</h2>
                    </div>
                    <div class="row category_selection_form">
                        <form method="post">
                            <?php
                            $select_category_query = "SELECT * FROM `categories`";
                            $run_select_category_query = mysqli_query($conn, $select_category_query);
                            while ($row = mysqli_fetch_assoc($run_select_category_query)) {
                                $category_id = $row['id'];
                                $category_name = $row['name'];
                                echo "<label><input type='radio' name='category[]' value='$category_id'> $category_name</label><br>";
                            }
                            ?>
                            <button type="reset" name="clear_btn" id="clear_btn"> Clear  Filters</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </script>
    <script type="module" src="../cf-admin/dependencies/cfusion/cfusion.js"></script>
     <script src="../scripts/js/programmes.js"></script>
</body>
</html>
