<?php
require '../cf-admin/server/scripts/php/config.php';

//extracting category rows 
$select_category_query = "SELECT * FROM `categories`";
                $run_select_category_query=mysqli_query($conn, $select_category_query);
                while($row=mysqli_fetch_assoc($run_select_category_query)){
                    $category_id = $row['id'];
                    $category_name = $row['name'];
                  
                }

//extracting products rows
$select_product_query = "SELECT * FROM `products`";
                $run_select_product_query=mysqli_query($conn, $select_product_query);
                while($row=mysqli_fetch_assoc($run_select_product_query)){
                  $product_id = $row['id'];
                  $product_name = $row['name'];
                  $product_description = $row['description'];
                  $product_pre_requirements = $row['pre_requirements'];
                  $Product_category_id = $row['category_id'];
                  $Product_vendor = $row['vendor'];
                  $Product_image_id = $row['image_id'];
                  $Product_credits = $row['credits'];
                  $Product_duration = $row['duration'];
                  $Product_level = $row['level'];
                  $Product_course_overview = $row['course_overview'];
                  $Product_program_structure = $row['program_structure'];
                  $Product_career_path = $row['career_path'];
                  $Product_student_guidance = $row['student_guidance'];
                  $Product_program_type = $row['program_type'];
                  $Product_study_mode = $row['study_mode'];
                  $Product_study_type = $row['study_type'];         
                }


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
                         <!-- programs comes here -->
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row level_selection">
                    <form method="post">
                        <label><input type="radio" name="olevel" value="olevel"> Male</label><br>
                        <label><input type="radio" name="alevel" value="alevel"> Female</label><br>
                        <label><input type="radio" name="diploma" value="diploma"> Other</label><br>
                        <label><input type="radio" name="other" value="other"> Other</label><br>

                        <input type="submit" value="Submit">
                    </form>


                </div>
            </div>
        </div>
    </div>

    </script>
    <!-- <script type="module" src="../cf-admin/dependencies/cfusion/cfusion.js"></script> -->
</body>
</html>
