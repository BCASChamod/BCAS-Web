const savedTheme = localStorage.getItem('theme');
if (savedTheme) {
  document.documentElement.setAttribute('data-theme', savedTheme);
} else {
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
}

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
