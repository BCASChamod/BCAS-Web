<?php
require 'server/scripts/php/config.php';
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: ./view/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFusion | Alpha</title>
    <meta name="robots" content="noindex, nofollow" />
    <link rel="stylesheet" href="./stylesheets/dashboard.css">
</head>
<body>
    <header>
        <h1>CFusion: Alpha Build</h1>
        <p>Alpha Build of CFusion Content Management System<br><b>BE ADVISED:</b> Still Under Development. Can Contain Bugs and Issues</p>
        <div class="main-panel">
            <button onclick="openpopup('.popup');">LiveEditor</button>
            <form action="./server/scripts/php/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <main>
        <div class="side-panel-container">
            <ul>
                <a href="mailto:chamothes@gmail.com"><li>Report a Issue</li></a>
                <a href="#"><li>Content Scheduler</li></a>
                <a href="#"><li>Activity Logs</li></a>
                <a href="#"><li>Buy Me a Coffee</li></a>
                <a href="#"><li>HTML Tag Manager</li></a>
                <a href="#"><li>Google Analytics</li></a>
            </ul>
        </div>
        <div class="maincard-container">
            <section id="optionPanel" class="option-panel">
                <div class="list-labels">
                    <h1>CM Panels</h1>
                    <p>Manage Your Content</p>
                </div>
                <div class="list-options">
                    <ul>
                        <a href="server/scripts/php/blogs.php"><li>Blog Posts</li></a>
                        <a href="#"><li>News and Events</li></a>
                        <a href="#"><li>Landing Pages</li></a>
                        <a href="#"><li>People</li></a>
                        <a href="#"><li>Products</li></a>
                        <a href="#"><li>Showcase</li></a>
                        <a href="#"><li>Media Library</li></a>
                    </ul>
                </div>
            </section>
            <section id="optionPanel" class="option-panel">
                <div class="list-labels">
                    <h1>Manage</h1>
                    <p>Your Website</p>
                </div>
                <div class="list-options">
                    <ul>
                        <a href="#"><li>User Management</li></a>
                        <a href="#"><li>SEO Settings</li></a>
                        <a href="#"><li>Site Settings</li></a>
                        <a href="#"><li>Data Collection</li></a>
                        <a href="#"><li>Disqus: Comment Management | Built-in Comment Manager</li></a>
                        <a href="#"><li>LiveChat/ChatBot Configuration</li></a>
                        <a href="#"><li>Special Effects</li></a>
                    </ul>
                </div>
            </section>
            <section  id="optionPanel" class="option-panel">
                <div class="list-options">
                    <ul>
                        <a href="#"><li>Advanced Settings</li></a>
                        <a href="#"><li>Developer Support</li></a>
                        <a href="#"><li>Feedback or Feature Request</li></a>
                    </ul>
                </div>
            </section>
        </div>
    </main>

    <section id="popups" class="popup-container">
        <div id="notAvailable" class="popup">
            <h3>Feature is <span style="color: red;">Not Available</span></h3>
            <p><span style="color: red;">Error:</span> Blocked by the Developer</p>
            <button onclick="closepopup();" style="margin-top: 40px;">Close</button>
        </div>
    </section>

    <footer>
        Developed by Chaze | Owned by REVAMPS
    </footer>
    <script src="./scripts/index.js"></script>
    <script type="module" src="./dependencies/cfusion/cfusion.js"></script>
</body>
</html>
