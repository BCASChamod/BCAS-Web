<?php
require '../cf-admin/server/scripts/php/config.php';

$getAbout = $conn->prepare("SELECT * FROM `about` ");
$getAbout ->execute();

$getAboutResult = $getAbout->get_result();

$about = $getAboutResult->fetch_all(MYSQLI_ASSOC);

?>
<script>
    window.productAllData = <?php echo json_encode($about); ?>;
    console.log(window.productAllData);
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="../stylesheets/programmes.css">
</head>
<body>






    <script type="module" src="../cf-admin/dependencies/cfusion/cfusion.js"></script>
     <script src="../scripts/js/programmes.js"></script>
</body>
</html>
