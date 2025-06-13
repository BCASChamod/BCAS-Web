export function injectHeader() {
  console.log("Injecting Header");

  const header = document.createElement("header");
  header.innerHTML = `
    <nav>
      <a href="#" aria-label="BCAS Logo">
        <img id="logo" src="" alt="" />
      </a>
      <ul>
        <li><a href="/">Programmes</a></li>
        <li><a href="">About BCAS</a></li>
        <li><a href="">Student Life</a></li>
        <li><a href="">Help & Support</a></li>
        <li><a href="">Insights</a></li>
      <div>
        <input type="checkbox" class="theme-switch" id="themeSwitch">
        <label for="themeSwitch" class="theme-switch-label">
          <i class="fa-solid fa-moon"></i>
          <i class="fa-solid fa-sun-bright"></i>
          <span class="ball"></span>
        </label>
      </div>
      </ul>
    </nav>
  `;
  document.body.insertBefore(header, document.body.firstChild);
}

export function injectFooter() {
  console.log("Injecting Footer");

  const footer = document.createElement("footer");
  footer.innerHTML = `
    <p>&copy; <?php echo date("Y"); ?> BCAS Campus. All rights reserved.</p>
  `;
  document.body.appendChild(footer);
}
