export function loadingOverlay() {
  // Debugging
  let debug = false;

  const log = (...args) => { if (debug) console.log(...args); };

  document.addEventListener('DOMContentLoaded', () => {
    const style = document.createElement('style');
    style.textContent = `
      #loading-overlay {
        position: fixed;
        inset: 0;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        transition: opacity 0.5s ease;
      }
      #loading-overlay .loader {
        width: 40px; height: 40px;
        border: 4px solid #ccc;
        border-top-color: #333;
        border-radius: 50%;
        animation: spin 1s linear infinite;
      }
      @keyframes spin { to { transform: rotate(360deg); } }
    `;
    document.head.appendChild(style);

    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.innerHTML = `<div class="loader"></div>`;
    document.body.appendChild(overlay);
    log('[loadingOverlay] Overlay added to DOM');
  });

  window.addEventListener('load', () => {
    const overlay = document.getElementById('loading-overlay');
    if (!overlay) {
      log('[loadingOverlay] Overlay not found');
      return;
    }

    // Only select media with a valid src and not lazy
    const medias = Array.from(document.querySelectorAll('img[src]:not([data-lazy]), video[src]:not([data-lazy])'));
    // Also include <video> with at least one <source src="..."> and not data-lazy
    document.querySelectorAll('video:not([data-lazy])').forEach(video => {
      if (!video.src && video.querySelector('source[src]')) {
        medias.push(video);
      }
    });

    let pending = medias.length;
    log(`[loadingOverlay] Found ${pending} media elements`);
    medias.forEach(m => {
      log('[loadingOverlay] Media:', m, 'Loaded:', m.tagName === 'IMG' ? m.complete : m.readyState > 0);
    });

    const tryRemove = () => {
      log(`[loadingOverlay] tryRemove called, pending: ${pending}`);
      if (pending <= 0) {
        overlay.style.opacity = '0';
        log('[loadingOverlay] Overlay opacity set to 0');
        const removeOverlay = () => {
          if (overlay.parentNode) {
            overlay.remove();
            log('[loadingOverlay] Overlay removed from DOM');
          }
        };
        overlay.addEventListener('transitionend', removeOverlay);
        setTimeout(removeOverlay, 700);
      }
    };

    medias.forEach(m => {
      const loaded = m.tagName === 'IMG' ? m.complete : m.readyState > 0;
      if (loaded) {
        pending--;
        log(`[loadingOverlay] Media already loaded:`, m);
      } else {
        m.addEventListener('load',  () => { pending--; log('[loadingOverlay] Media loaded:', m); tryRemove(); });
        m.addEventListener('error', () => { pending--; log('[loadingOverlay] Media error:', m); tryRemove(); });
      }
    });

    // Always remove overlay after 5 seconds (failsafe)
    setTimeout(() => {
      pending = 0;
      tryRemove();
    }, 5000);

    tryRemove();
  });
}
