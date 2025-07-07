<?php
require '../cf-admin/server/scripts/php/config.php';

$eventStmt = $conn->prepare("SELECT * FROM newsfeed WHERE is_active = 1 ORDER BY created_at");
$eventStmt->execute();
$eventResult = $eventStmt->get_result();

$eventData = $eventResult->fetch_all(MYSQLI_ASSOC);
$latestNews = $eventData[0] ?? null;
?>

<script>
  window.eventData = <?php echo json_encode($eventData); ?>;
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Events</title>
    <link rel="stylesheet" href="../stylesheets/global.css">
    <link rel="stylesheet" href="../stylesheets/newsfeed.css">
</head>
<body>
    <div class="main-wrapper" id="mainWrapper">
        <main>
            <div class="news-wrapper">
                <div class="icons">
                    <button id="newsBtnPrevious"><i class="fa-solid fa-circle-chevron-left icon" title="Previous"></i></button>
                    <button id="newsBtnNext"><i class="fa-solid fa-circle-chevron-right icon" title="Next"></i></button>
                </div>

                <div id="newsContainer" class="news-container">

                </div>
            </div>
            <h3 class="event-header">Events</h3>
            <div id="eventContainer" class="event-container">
                <div id="eventitem" class="event-item">
                    <img id="${program.image_id}" src="" alt="Program Image" />
                    <div class="content">
                        <h3>BCAS Avurudu 25</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorem, aspernatur.</p>
                    </div>
                </div>

                <div id="eventitem" class="event-item">
                    <img id="${program.image_id}" src="" alt="Program Image" />
                    <div class="content">
                        <h3>BCAS Convocation 2025</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nihil, voluptas?</p>
                    </div>
                </div>               

            </div>
        </main>

        <aside>

        </aside>
    </div>


<script type="module" src="../cf-admin/dependencies/cfusion/cfusion.js"></script>
<script src="../scripts/js/newsfeed.js"></script>
</body>
</html>