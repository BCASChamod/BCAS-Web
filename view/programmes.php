<?php
    require '../cf-admin/server/scripts/php/config.php';
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
            <!-- Undergraduate Header -->
            <h1><a href="#" onclick="toggleVisibility('undergraduateList')">Undergraduate</a></h1>
            <!-- Undergraduate Programs List -->
            <div id="undergraduateList" style="display: none;">
                <?php
                    $selectQuery = "SELECT * FROM `products` WHERE `program_type` = 'Undergraduate'";
                    $run_selectQuery = mysqli_query($conn, $selectQuery);

                    if ($run_selectQuery && mysqli_num_rows($run_selectQuery) > 0) {
                        echo '<ul>';
                        while($row = mysqli_fetch_assoc($run_selectQuery)){
                            echo '<li>' . htmlspecialchars($row['name']) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No undergraduate programmes found.</p>';
                    }
                ?>
            </div>

            <!-- Postgraduate Header -->
            <h1><a href="#" onclick="toggleVisibility('postgraduateList')">Postgraduate</a></h1>
            <!-- Postgraduate Programs List -->
            <div id="postgraduateList" style="display: none;">
                <?php
                    $selectQuery = "SELECT * FROM `products` WHERE `program_type` = 'Postgraduate'";
                    $run_selectQuery = mysqli_query($conn, $selectQuery);

                    if ($run_selectQuery && mysqli_num_rows($run_selectQuery) > 0) {
                        echo '<ul>';
                        while($row = mysqli_fetch_assoc($run_selectQuery)){
                            echo '<li>' . htmlspecialchars($row['name']) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>No postgraduate programmes found.</p>';
                    }
                ?>
            </div>
        </section>

        <section class="course-container" id="courseContainer">
            <!-- Additional dynamic content if needed -->
        </section>
    </main>

    <aside>
        <!-- Sidebar if needed -->
    </aside>

    <!-- JS Files -->
    <script type="module" src="http://localhost/bcas-web/cf-admin/dependencies/cfusion/cfusion.js"></script>
    <script type="module" src="http://localhost/bcas-web/scripts/js/global.js"></script>

    <!-- Toggle Script -->
    <script>
        function toggleVisibility(id) {
            const section = document.getElementById(id);
            const allSections = ['undergraduateList', 'postgraduateList'];

            allSections.forEach(sec => {
                if (sec !== id) {
                    document.getElementById(sec).style.display = 'none';
                }
            });

            section.style.display = (section.style.display === 'none') ? 'block' : 'none';
        }
    </script>
</body>
</html>
