<?php
require './cf-admin/server/scripts/php/config.php';

$btnBarStmt = $conn->prepare("SELECT id, label, custom_styles, action, action_rest, helptext FROM ui_elements WHERE source = 'landing_btnbar' AND is_active = 1");
$eventStmt = $conn->prepare("SELECT title, date, location, created_at, coverimg FROM news_and_events WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3");
$productStmt = $conn->prepare("SELECT id, name, level, program_type FROM products WHERE is_active = 1");

$btnBarStmt->execute();
$btnBarResult = $btnBarStmt->get_result();

$eventStmt->execute();
$eventResult = $eventStmt->get_result();

$productStmt->execute();
$productResult = $productStmt->get_result();

$productData = $productResult->fetch_all(MYSQLI_ASSOC);
?>
<script>
  window.productData = <?php echo json_encode($productData); ?>;
</script>

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
      <div id="btnBar" class="btn-bar">
        <?php
          while ($row = $btnBarResult->fetch_assoc()) {
            if (isset($row['is_active']) && !$row['is_active']) {
              continue;
            }
            $actionAttr = '';
            if ($row['action'] === 'link') {
              $actionAttr = 'onclick="location.href=\'' . htmlspecialchars($row['action_rest']) . '\'"';
            } elseif ($row['action'] === 'custom_javascript') {
              $actionAttr = 'onclick="' . htmlspecialchars($row['action_rest']) . '"';
            }
            $customStyles = '';
            if (!empty($row['custom_styles'])) {
              $styles = json_decode($row['custom_styles'], true);
              if (is_array($styles)) {
          foreach ($styles as $key => $value) {
            $cssKey = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $key));
            $customStyles .= htmlspecialchars($cssKey) . ': ' . htmlspecialchars($value) . '; ';
          }
              }
            }
            if (!empty($customStyles)) {
              $customStyles = 'style="' . trim($customStyles) . '" ';
            }
            $helptext = !empty($row['helptext']) ? ' title="' . htmlspecialchars($row['helptext']) . '"' : '';
            echo '<button class="" ' . $customStyles . $actionAttr . $helptext . '>' . htmlspecialchars($row['label']) . '</button>';
          }
          $btnBarStmt->close();
        ?>
      </div>
      </div>
    </section>

    <section id="searchSection" class="search-section container-fluid">
      <div class="row align-items-center">
        <div class="col-md-8">
          <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search..." />
            <button id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
          </div>
        </div>
        <div class="col-md-4 text-end">
          <div class="cta-section">
            <h4>Explore your journey with BCAS</h4>
            <button>Start Now</button>
          </div>
        </div>
      </div>
    </section>

    <section class="menu-section">
      <h2>Explore BCAS</h2>
      <div class="main-menu">
        <div class="card card--big">
          <div class="text-content">
            <p>Explore</p>
            <h2>Our Programmes</h2>
          </div> 
          <img id="programmesMain" src="" alt="">
        </div>
        <div class="card card--top1">
          <div class="text-content">
            <?php
            if ($eventRow = $eventResult->fetch_assoc()) {
              echo '<p>Latest Event</p>';
              echo '<h2>' . htmlspecialchars($eventRow['title']) . '</h2>';
              echo '</div>';
              $coverImg = !empty($eventRow['coverimg']) ? htmlspecialchars($eventRow['coverimg']) : '';
              echo '<img id="' . $coverImg . '" src="" alt="">';
            } else {
              echo '<p>No events found</p>';
              echo '</div>';
              echo '<img id="placeholder" src="" alt="">';
            }
            ?>
        </div>

        <div class="card card--top2">
          <div class="text-content">
            <p>Talk to a</p>
            <h2>Student Counselor</h2>
          </div>
          <img id="talkToCounselor" src="" alt="">
        </div>

        <div class="card card--wide">
          <div class="text-content">
            <p>Our Story</p>
            <h2>BCAS Campus</h2>
          </div>
          <img id="aboutUs" src="" alt="">
        </div>
      </div>
    </section>
    
    <div class="content-wrapper">

      <section class="grd-con short-about" id="shortAbout">
        <div class="grd-item short-about-image">
          <img id="bcasBuilding" src="" alt="" />
        </div>
        <div class="grd-item short-about-text">
          <h2>25 Years of Excellence in Education</h2>
          <p>
            For over 25 years, BCAS has been shaping futures with a strong commitment to quality education and student success. Our institution provides students with a unique blend of character development and academic vision, empowering them to pursue rewarding careers. By working closely with industry partners, BCAS ensures students gain real-world skills and valuable experience. It’s the perfect place to start your journey toward a successful and impactful future.
          </p>
        </div>
      </section>

      <section class="awards-section">
        <h2 class="timeline-header">
          Consistently Recognized for Excellence in Education
        </h2>
        <div id="timelineContainer" class="award-timeline">
          <svg id="Timeline" class="timeline-svg" style="width: 3444px; height: 600px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3000 391.43">
            <g id="item-up" class="timeline-item">
              <path id="Pointer" class="svg-inneritem" d="M82.52,217.37h-10V69.99c0-2.76,2.24-5,5-5h0c2.76,0,5,2.24,5,5v147.38ZM82.52,217.37h-10l5,7.62,5-7.62Z"/>
              <text class="svg-innertext" transform="translate(38.69 259.31)"><tspan x="0" y="0">2000</tspan></text>
              <text class="svg-innersubtext" transform="translate(93.2 75.22)"><tspan x="0" y="0">Founding of BCAS</tspan></text>
            </g>
            <g id="item-down" class="timeline-item"> 
              <path id="Pointer-2" data-name="Pointer" class="svg-inneritem" d="M292.71,178.37h10v147.38c0,2.76-2.24,5-5,5h0c-2.76,0-5-2.24-5-5v-147.38ZM292.71,178.37h10s-5-7.62-5-7.62l-5,7.62Z"/>
              <text class="svg-innertext" transform="translate(263.09 167.43)"><tspan x="0" y="0">2010</tspan></text>
              <text class="svg-innersubtext" transform="translate(133.68 290.82)"><tspan x="0" y="0">Performance excellence</tspan><tspan x="26.42" y="16.8">The fastest growing</tspan><tspan x="-18.79" y="33.6">Edexcel Center in Sri Lanka</tspan></text>
            </g>
            <g id="item-up-2" data-name="item-up" class="timeline-item">
              <path id="Pointer-3" data-name="Pointer" class="svg-inneritem" d="M420.14,217.37h-10V69.99c0-2.76,2.24-5,5-5h0c2.76,0,5,2.24,5,5v147.38ZM420.14,217.37h-10l5,7.62,5-7.62Z"/>
              <text class="svg-innertext" transform="translate(384.74 259.31)"><tspan x="0" y="0">2011</tspan></text>
              <text class="svg-innersubtext" transform="translate(431.52 75.22)"><tspan x="0" y="0">Best performance in </tspan><tspan x="0" y="16.8">Sri Lanka in 2011 by </tspan><tspan x="0" y="33.6">Edexcel - UK</tspan></text>
            </g>
            <g id="item-down-2" data-name="item-down" class="timeline-item">
              <path id="Pointer-4" data-name="Pointer" class="svg-inneritem" d="M613.27,178.37h10v147.38c0,2.76-2.24,5-5,5h0c-2.76,0-5-2.24-5-5v-147.38ZM613.27,178.37h10s-5-7.62-5-7.62l-5,7.62Z"/>
              <text class="svg-innertext" transform="translate(582.41 167.43)"><tspan x="0" y="0">2013 </tspan></text>
              <text class="svg-innersubtext" transform="translate(632.42 310.56)"><tspan x="0" y="0">“BTEC Gold Partner” </tspan><tspan x="0" y="16.8">presented by Edexcel - UK</tspan></text>
            </g>
            <g id="item-up-3" data-name="item-up" class="timeline-item">
              <path id="Pointer-5" data-name="Pointer" class="svg-inneritem" d="M843.95,215.8h-10V11.27c0-2.76,2.24-5,5-5h0c2.76,0,5,2.24,5,5v204.52ZM843.95,215.8h-10l5,10.48,5-10.48Z"/>
              <text class="svg-innertext" transform="translate(805.2 259.31)"><tspan x="0" y="0">2014</tspan></text>
              <text class="svg-innersubtext" transform="translate(855.66 25.66)"><tspan x="0" y="0">The Education Service Excellence </tspan><tspan x="0" y="16.8">Award by United Nations Decade </tspan><tspan x="0" y="33.6">of Education for sustainable </tspan><tspan x="0" y="50.4">Development (UN-DESD)</tspan></text>
              <text class="svg-innersubtext" transform="translate(855.66 122.02)"><tspan x="0" y="0">Recognition by LMD Magazine as the </tspan><tspan x="0" y="16.8">‘Number-1 private higher education </tspan><tspan x="0" y="33.6">institute’</tspan></text>
              <path class="svg-separator {" d="M856,92.02h226.75c.77,0,1.4.63,1.4,1.4h0c0,.77-.63,1.4-1.4,1.4h-226.75v-2.8h0Z"/>
            </g>
            <g id="item-down-3" data-name="item-down" class="timeline-item">
              <path id="Pointer-6" data-name="Pointer" class="svg-inneritem" d="M1140.14,178.37h10v147.38c0,2.76-2.24,5-5,5h0c-2.76,0-5-2.24-5-5v-147.38ZM1140.14,178.37h10s-5-7.62-5-7.62l-5,7.62Z"/>
              <text class="svg-innertext" transform="translate(1112.06 167.43)"><tspan x="0" y="0">2015</tspan></text>
              <text class="svg-innersubtext" transform="translate(1159.29 290.56)"><tspan x="0" y="0">Platinum Partner </tspan><tspan x="0" y="16.8">No.1 BTEC Centre</tspan><tspan x="0" y="33.6">in Sri Lanka</tspan></text>
            </g>
            <g id="item-up-4" data-name="item-up" class="timeline-item">
              <path id="Pointer-7" data-name="Pointer" class="svg-inneritem" d="M1346.52,214.42h-10V7.96c0-2.76,2.24-5,5-5h0c2.76,0,5,2.24,5,5v206.46ZM1346.52,214.42h-10l5,10.57,5-10.57Z"/>
              <text class="svg-innertext" transform="translate(1308.28 259.31)"><tspan x="0" y="0">2016</tspan></text>
              <text class="svg-innersubtext" transform="translate(1357.89 75.22)"><tspan x="0" y="0">Visionary Leadership Award </tspan><tspan x="0" y="16.8">for BCAS Chairman BCAS</tspan></text>
              <text class="svg-innersubtext" transform="translate(1357.89 21.73)"><tspan x="0" y="0">Platinum Partner No.1 BTEC </tspan><tspan x="0" y="16.8">Centre in Sri Lanka</tspan></text>
              <text class="svg-innersubtext" transform="translate(1357.89 126.16)"><tspan x="0" y="0">Educational Institute with Best</tspan><tspan x="0" y="16.8">Academic &amp; Industry Interface</tspan></text>
              <path class="svg-separator {" d="M1358.62,103.31h195.29c.77,0,1.4.63,1.4,1.4h0c0,.77-.63,1.4-1.4,1.4h-195.29v-2.8h0Z"/>
              <path class="svg-separator {" d="M1358.62,50.46h195.29c.77,0,1.4.63,1.4,1.4h0c0,.77-.63,1.4-1.4,1.4h-195.29v-2.8h0Z"/>
            </g>
            <g id="item-down-4" data-name="item-down" class="timeline-item">
              <path id="Pointer-8" data-name="Pointer-down" class="svg-inneritem" d="M1615.58,181.26h10v205.16c0,2.76-2.24,5-5,5h0c-2.76,0-5-2.24-5-5v-205.16ZM1615.58,181.26h10s-5-10.51-5-10.51l-5,10.51Z"/>
              <text class="svg-innertext" transform="translate(1588.09 167.43)"><tspan x="0" y="0">2017</tspan></text>
              <text class="svg-innersubtext" transform="translate(1634.73 346.56)"><tspan x="0" y="0">Best Employer Branch Award</tspan></text>
              <text class="svg-innersubtext" transform="translate(1634.73 381.34)"><tspan x="0" y="0">Gold Award Pearson, UK</tspan></text>
              <text class="svg-innersubtext" transform="translate(1634.73 265.39)"><tspan x="0" y="0">Excellence in Training Award </tspan><tspan x="0" y="16.8">(Overall Award based on results </tspan><tspan x="0" y="33.6">based training Asia Pacific HRM </tspan><tspan x="0" y="50.4">Congress)</tspan></text>
              <path class="svg-separator {" d="M1635.79,325.64h195.29c.77,0,1.4.63,1.4,1.4h0c0,.77-.63,1.4-1.4,1.4h-195.29v-2.8h0Z"/>
              <path class="svg-separator {" d="M1635.79,357.31h195.29c.77,0,1.4.63,1.4,1.4h0c0,.77-.63,1.4-1.4,1.4h-195.29v-2.8h0Z"/>
            </g>
            <g id="item-up-5" data-name="item-up" class="timeline-item">
              <path id="Pointer-9" data-name="Pointer" class="svg-inneritem" d="M1908.56,214.42h-10V7.96c0-2.76,2.24-5,5-5h0c2.76,0,5,2.24,5,5v206.46ZM1908.56,214.42h-10l5,10.57,5-10.57Z"/>
              <text class="svg-innertext" transform="translate(1870.47 259.31)"><tspan x="0" y="0">2018</tspan></text>
              <text class="svg-innersubtext" transform="translate(1919.94 11.73)"><tspan x="0" y="0">Best Employer Brand Award, </tspan><tspan x="0" y="16.8">Outstanding Contribution </tspan><tspan x="0" y="33.6">to the cause of Education, </tspan><tspan x="0" y="50.4">Award for Excellence in Training</tspan><tspan x="0" y="67.2">(World HRD Congress 13th Employers </tspan><tspan x="0" y="84">Branding Award Le Meridian, Singapore)</tspan></text>
              <text class="svg-innersubtext" transform="translate(1919.94 126.16)"><tspan x="0" y="0">Gold Award Pearson, UK</tspan></text>
              <path class="svg-separator {" d="M1920.65,106.31h258.6c.77,0,1.4.63,1.4,1.4h0c0,.77-.63,1.4-1.4,1.4h-258.6v-2.8h0Z"/>
            </g>
            <g id="item-down-5" data-name="item-down" class="timeline-item">
              <path id="Pointer-10" data-name="Pointer" class="svg-inneritem" d="M2255.5,175.73h10v94.53c0,2.76-2.24,5-5,5h0c-2.76,0-5-2.24-5-5v-94.53ZM2255.5,175.73h10s-5-4.98-5-4.98l-5,4.98Z"/>
              <text class="svg-innertext" transform="translate(2227.26 167.43)"><tspan x="0" y="0">2019</tspan></text>
              <text class="svg-innersubtext" transform="translate(2276.37 253.33)"><tspan x="0" y="0">Gold Award </tspan><tspan x="0" y="16.8">Pearson, UK</tspan></text>
            </g>
            <g id="item-up-6" data-name="item-up" class="timeline-item">
              <path id="Pointer-11" data-name="Pointer" class="svg-inneritem" d="M2455.44,220.91h-10v-76.61c0-2.76,2.24-5,5-5h0c2.76,0,5,2.24,5,5v76.61ZM2455.44,220.91h-10l5,4.08,5-4.08Z"/>
              <text class="svg-innertext" transform="translate(2413.03 259.31)"><tspan x="0" y="0">2020</tspan></text>
              <text class="svg-innersubtext" transform="translate(2468.24 150.28)"><tspan x="0" y="0">Gold Award </tspan><tspan x="0" y="16.8">Pearson, UK</tspan></text>
            </g>
            <g id="item-down-6" data-name="item-down" class="timeline-item">
              <path id="Pointer-12" data-name="Pointer" class="svg-inneritem" d="M2635.16,175.73h10v94.53c0,2.76-2.24,5-5,5h0c-2.76,0-5-2.24-5-5v-94.53ZM2635.16,175.73h10s-5-4.98-5-4.98l-5,4.98Z"/>
              <text class="svg-innertext" transform="translate(2604.18 167.43)"><tspan x="0" y="0">2022</tspan></text>
              <text class="svg-innersubtext" transform="translate(2656.03 253.33)"><tspan x="0" y="0">Gold Award </tspan><tspan x="0" y="16.8">Pearson, UK</tspan></text>
            </g>
            <g id="item-up-7" data-name="item-up" class="timeline-item">
              <path id="Pointer-13" data-name="Pointer" class="svg-inneritem" d="M2812.67,220.91h-10v-76.61c0-2.76,2.24-5,5-5h0c2.76,0,5,2.24,5,5v76.61ZM2812.67,220.91h-10l5,4.08,5-4.08Z"/>
              <text class="svg-innertext" transform="translate(2771.8 259.31)"><tspan x="0" y="0">2023</tspan></text>
              <text class="svg-innersubtext" transform="translate(2825.48 150.28)"><tspan x="0" y="0">Gold Award </tspan><tspan x="0" y="16.8">Pearson, UK</tspan></text>
            </g>
            <g id="main-branch">
              <path class="main-branch" d="M3000,207.47H5c-2.76,0-5-2.24-5-5h0c0-2.76,2.24-5,5-5h2995v10Z"/>
            </g>
          </svg>
        </div>
      </section>

      <section class="grd-con testimonial-section" id="testimonials">
        <div class="grd-item testimonial-text">
          <h2>One Story Closer to a Dream Career</h2>
          <p>Watch how our students turn their dreams into real career paths through practical learning, expert guidance, and industry-relevant skills at BCAS Campus.</p>
          <div class="testimonial-controls">
            <div class="caption-controls">
              <i class="fa-solid fa-closed-captioning"></i>
              <select id="testCaption" name="captions" id="videoCaption">
                <option value="" selected>Select CC</option>
                <option value="en">English</option>
                <option value="si">සිංහල</option>
                <option value="ta">தமிழ்</option>
              </select>
            </div>
            <div>
            <i id="volumeIndicator" class="fa-solid fa-volume"></i>
            <input type="range" id="videoVolume" min="0" max="1" value="0" step="0.01">
            </div>
          </div>
          <div class="seekbar-container">
            <input type="range" id="testimonialSeekbar" min="0" max="100" value="0" step="0.01" style="width: 100%;">
          </div>
        </div>
        <div class="grd-item testimonial-video">
          <video id="testimonialVideo" data-lazy="true" data-src="http://localhost/bcas-web/resources/uploads/videos/testimonials_1.webm" autoplay muted>
            <source type="video/webm">
            <track kind="captions" src="http://localhost/bcas-web/resources/uploads/captions/testimonials_1.vtt" srclang="en" label="English" default>
            <track kind="captions" src="http://localhost/bcas-web/resources/uploads/captions/si-testimonials_1.vtt" srclang="si" label="Sinhala">
            <track kind="captions" src="http://localhost/bcas-web/resources/uploads/captions/ta-testimonials_1.vtt" srclang="ta" label="Tamil">
            Sorry, your browser doesn't support embedded videos.
          </video>
        </div>
      </section>

    </div>

    <section class="guidance-section">
      <div class="guidance-content" style="text-align: center;">
        <h2>Guidance for Your Future</h2>
        <p>At BCAS, we believe in nurturing the potential of every student. Our expert counselors are here to guide you through your educational journey, helping you make informed decisions about your future.</p>
      </div>
      <div class="form-container">
          <div class="left-panel">
            <div class="left-panel-content">
              <h1>Need Guidance?</h1>
              <p>Let’s talk about your future.<br>Fill out the form & we’ll reach out to guide you.</p>
            </div>
            <div class="image-placeholder">
              <img id="formPending" class="form-pending" src="" alt="">
              <img id="formSuccess" class="form-success" src="" alt="">
            </div>
          </div>
          <div id="formArea" data-submission="pre" class="form-section">
          
          </div>
      </div>
    </section>

    <section class="moretogo">
      <h3>Latest Posts</h3>
      <div class="wrapper">
        
        <div class="item">
          text item
        </div>
        <div class="item">
          text item
        </div>
        <div class="item">
          text item
        </div>
      </div>
    </section>
  </main>


  <footer class="site-footer">
  <div class="slogan-container">
    <h4><span>Building Careers</span><br><span>Transforming Lives</span></h4>
  </div>
    <div class="container-fluid footer-container">
      <div class="row footer-row">
        <div class="col-md-4 footer-col">
          <h4>BCAS Campus</h4>
          <p>Empowering tomorrow's leaders through world-class education for over 25 years.</p>
          <ul class="footer-social">
            <li><a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a></li>
            <li><a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a></li>
            <li><a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a></li>
            <li><a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a></li>
          </ul>
        </div>
        <div class="col-md-4 footer-col">
          <h4>Quick Links</h4>
          <ul class="footer-links">
            <li><a href="/">Home</a></li>
            <li><a href="/programmes">Programmes</a></li>
            <li><a href="/about">About Us</a></li>
            <li><a href="/news">News & Events</a></li>
            <li><a href="/contact">Contact</a></li>
          </ul>
        </div>
        <div class="col-md-4 footer-col">
          <h4>Contact Us</h4>
          <ul class="footer-contact">
            <li><i class="fa-solid fa-location-dot"></i> 256, Galle Road, Colombo 06, Sri Lanka</li>
            <li><i class="fa-solid fa-phone"></i> +94 11 236 0978</li>
            <li><i class="fa-solid fa-envelope"></i> info@bcas.lk</li>
          </ul>
        </div>
      </div>
      <div class="row">
        <div class="col-12 text-center footer-bottom">
          <p>&copy; <?php echo date('Y'); ?> BCAS Campus. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>


  <script src="./scripts/js/index.js"></script>
  <script type='module' src='./cf-admin/dependencies/cfusion/cfusion.js'></script>
  <script src="./scripts/js/global.js"></script>

</body>
</html>