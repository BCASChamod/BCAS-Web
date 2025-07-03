export function injectHeader() {
  console.log("Injecting Header");

  const header = document.createElement("header");
  header.innerHTML = `
    <nav>
    <div class="desktop-logo">
      <a class="logo-container" href="#" aria-label="BCAS Logo">
        <img id="logo" class="logo" src="" alt="" />
      </a>
    </div>
      <ul class="desktop-nav-content">

        <li class="dropdown"><span class="nav-gate">Programmes <i class="fa-regular fa-angle-down"></i></span>
          <div class="dropdown-container">
            <div class="dropdown-content">
              <a class="nav-link" href="http://localhost/bcas-web/view/programmes.php?activetab=undergraduate">Undergraduate</a>
              <a class="nav-link" href="http://localhost/bcas-web/view/programmes.php?activetab=postgraduate">Postgraduate</a>
            </div>
          </div>
        </li>

        <li class="dropdown"><a class="nav-gate" href="/">About BCAS <i class="fa-regular fa-angle-down"></i></a></i>
          <div class="dropdown-container">
            <div class="dropdown-content">
              <a class="nav-link" href="#">About Us</a>
              <a class="nav-link" href="#">Board of Governers</a>
              <a class="nav-link" href="#">Head Staff</a>
              <a class="nav-link" href="#">Affliations & Partners</a>
            </div>
          </div>
        </li>

        <li class="dropdown"><a class="nav-gate" href="/">Student Life <i class="fa-regular fa-angle-down"></i></a></i>
          <div class="dropdown-container">
            <div class="dropdown-content">
              <a class="nav-link" href="#">Forum</a>
              <a class="nav-link" href="#">News & Events</a>
              <a class="nav-link" href="#">Facilities</a>
              <a class="nav-link" href="#">Clubs & Society</a>
            </div>
          </div>
        </li>

        <li class="dropdown"><a class="nav-gate" href="/">Support & Services <i class="fa-regular fa-angle-down"></i></a></i>
          <div class="dropdown-container">
            <div class="dropdown-content">
              <a class="nav-link" href="#">Virtual Learning Environment</a>
              <a class="nav-link" href="#">Student Support</a>
              <a class="nav-link" href="#">Certificate Verification</a>
              <a class="nav-link" href="#">Payments</a>
              <a class="nav-link" href="#">Careers</a>
            </div>
          </div>
        </li>

        <li class="dropdown"><a class="nav-gate" href="/">Insights <i class="fa-regular fa-angle-down"></i></a></i>
          <div class="dropdown-container">
            <div class="dropdown-content">
              <a class="nav-link" href="#">Blogs</a>
              <a class="nav-link" href="#">Career Guidance</a>
            </div>
          </div>
        </li>

      </ul>

      <button class="nav-toggle" aria-label="Toggle navigation">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>

    <div class="desktop-theme-switch">
      <input type="checkbox" class="theme-switch" id="themeSwitch">
      <label for="themeSwitch" class="theme-switch-label">
        <i class="fa-solid fa-moon"></i>
        <i class="fa-solid fa-sun-bright"></i>
        <span class="ball"></span>
      </label>
    </div>

    </nav>
  `;
  document.body.insertBefore(header, document.body.firstChild);
}

export function injectFooter() {
  console.log("Injecting Footer");

  const footer = document.createElement("footer");
  footer.className = 'site-footer';
  footer.innerHTML = `
    <div class="container-fluid footer-container">
      <div class="row footer-row">
        <div class="col-md-4 footer-col">
          <img id="logo" src="" alt="" class="logo" style="max-width: 160px; margin-bottom: 1rem;" />
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
            <li><i class="fa-solid fa-location-dot"></i> <span class="branch-address">356, Galle Road, Colombo 03, Sri Lanka</span></li>
            <li><i class="fa-solid fa-phone"></i> <span class="branch-phone">+94 117 999 300</span></li>
            <li><i class="fa-solid fa-envelope"></i> <span class="branch-email">info@bcas.lk</span></li>
          </ul>
        </div>
      </div>
      <div class="row">
        <div class="col-12 text-center footer-bottom">
          <p>&copy; <span id="footer-year"></span> BCAS Campus. All rights reserved.</p>
        </div>
      </div>
    </div>
  `;
  document.body.appendChild(footer);

  const yearSpan = footer.querySelector('#footer-year');
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }
}
