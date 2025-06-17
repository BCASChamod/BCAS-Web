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

function autoscroll(elementOrId, loop = false, direction = 'down', speed = 1) {
  const element = typeof elementOrId === 'string'
    ? document.getElementById(elementOrId)
    : elementOrId;
  if (!element) return;

  let dir = direction.toLowerCase();
  let rafId;

  function step() {
    let deltaX = 0, deltaY = 0;
    if (dir === 'down')      deltaY = speed;
    else if (dir === 'up')   deltaY = -speed;
    else if (dir === 'right') deltaX = speed;
    else if (dir === 'left')  deltaX = -speed;
    else {
      console.warn('[autoscroll] bad direction:', dir);
      return;
    }

    element.scrollBy(deltaX, deltaY);

    const atBottom  = element.scrollTop  + element.clientHeight >= element.scrollHeight;
    const atTop     = element.scrollTop  <= 0;
    const atRight   = element.scrollLeft + element.clientWidth  >= element.scrollWidth;
    const atLeft    = element.scrollLeft <= 0;

    let hitEdge = false;
    if ((dir === 'down'  && atBottom)  ||
        (dir === 'up'    && atTop)     ||
        (dir === 'right' && atRight)   ||
        (dir === 'left'  && atLeft)) {
      hitEdge = true;
    }

    if (hitEdge) {
      if (loop) {
        if (dir === 'down')      dir = 'up';
        else if (dir === 'up')   dir = 'down';
        else if (dir === 'right') dir = 'left';
        else if (dir === 'left')  dir = 'right';
      } else {
        cancelAnimationFrame(rafId);
        return;
      }
    }

    rafId = requestAnimationFrame(step);
  }

  rafId = requestAnimationFrame(step);

  return {
    stop() {
      cancelAnimationFrame(rafId);
    }
  };
}



const timelineContainer = document.getElementById('timelineContainer');
timelineContainer.scrollLeft = 100;

autoscroll(timelineContainer, true, 'right', 1.2);
