function updateLogo() {
  const logo = document.getElementById('logo');
  if (!logo) return;
  const theme = document.documentElement.getAttribute('data-theme');
  logo.id = theme === 'dark' ? 'darkLogo' : 'lightLogo';
}
updateLogo();

function setupThemeSwitch(checkbox) {
  checkbox.checked = document.documentElement.getAttribute('data-theme') === 'light';
  checkbox.addEventListener('change', () => {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    setTimeout(() => window.location.reload(), 500);
  });
}

const observer = new MutationObserver((mutations, obs) => {
  updateLogo();
  const checkbox = document.getElementById('themeSwitch');
  if (checkbox) {
    setupThemeSwitch(checkbox);
    obs.disconnect();
  }
});

observer.observe(document.body, { childList: true, subtree: true });


window.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.card').forEach(card => {
    const overlay = document.querySelector('.main-overlay');
    if (!overlay) return;
    card.addEventListener('mouseover', () => {
      overlay.style.display = "block";
      setTimeout(() => {
        overlay.style.opacity = 0.5;
      }, 200);
    });
    card.addEventListener('mouseout', () => {
      overlay.style.opacity = 0;
      setTimeout(() => {
      overlay.style.display = "none";
      }, 200);
    });
  });
});
