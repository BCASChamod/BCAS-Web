export function injectHeader() {
  console.log("Injecting Header");

  const header = document.createElement("header");
  header.innerHTML = `
    <nav id="mainNav" class="main-nav">
      <div class="navmenu-container">
        <div class="navlogo">
          <img src="" alt="">
        </div>
        <ul class="navmenu">
          <li><i class="fa-light fa-home-simple navicon"></i>Home</li>
          <li><i class="fa-light fa-address-card navicon"></i>About</li>
          <li><i class="fa-light fa-bell-concierge navicon"></i>Services</li>
          <li><i class="fa-light fa-cart-flatbed navicon"></i>Shop</li>
          <li><i class="fa-light fa-briefcase navicon"></i>Portfolio</li>
          <li><i class="fa-light fa-address-card navicon"></i>Contact</li>
        </ul>
        <ul class="navql">
          <li>Blog</li>
          <li>FAQs</li>
          <li>Help & Support</li>
        </ul>
      </div>
      <div id="navTrigger" class="nav-trigger">
        <i class="fa-regular fa-grip-lines"></i>
      </div>
    </nav>
  `;
  document.body.insertBefore(header, document.body.firstChild);
}

export function injectFooter() {
  console.log("Injecting Footer");

  const footer = document.createElement("footer");
  footer.innerHTML = `
    <div class="footer-container">
      <p>&copy; 2025 MySite. All rights reserved.</p>
      <ul class="footer-links">
        <li><a href="#">Privacy</a></li>
        <li><a href="#">Terms</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </div>
  `;
  document.body.appendChild(footer);
}
