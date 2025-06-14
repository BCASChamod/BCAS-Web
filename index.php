<?php
// index.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BCAS Website</title>
  <link rel="stylesheet" href="./stylesheets/global.css">
  <link rel="stylesheet" href="./stylesheets/index.css">
</head>
<body>

  <main>
    <section id="landing" class="landing">
      <div class="landing-bg">
        <img id="landingBg" src="" alt="">
        <div id="landingText" class="landing-text">
          <h2>
            Empowering tommorow's leaders through<br><span>World-Class Education</span>
          </h2>
        </div>
        <img id="landingSubjects" src="" alt="">
      </div>
    </section>

    <section id="searchSection" class="search-section">
      <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search..." />
        <button id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
      </div>
      <div class="cta-section">
        <h4>Explore your journey with BCAS</h4>
        <button>Start Now</button>
      </div>
    </section>

    <section class="menu-section">
      <h2>Explore BCAS</h2>
      <div class="main-menu">
        <div class="card card--big">Our Programmes</div>
        <div class="card card--top1">Open Day Highlights</div>
        <div class="card card--top2">Talk to a Counselor</div>
        <div class="card card--wide">BCAS Campus</div>
      </div>
    </section>

    <section class="dummy"></section>
  </main>



  <script src="./scripts/js/index.js"></script>
  <script type='module' src='./cf-admin/dependencies/cfusion/cfusion.js'></script>
  <script src="./scripts/js/global.js"></script>

</body>
</html>