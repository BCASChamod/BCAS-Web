(async function() {

  function waitForBody() {
    return new Promise((resolve) => {
      if (document.body) {
        resolve();
      } else {
        const observer = new MutationObserver((mutations, me) => {
          if (document.body) {
            me.disconnect();
            resolve();
          }
        });
        observer.observe(document.documentElement, { childList: true });
      }
    });
  }

  await waitForBody();

  window.name = "initiated!";
  var iframe = document.createElement('iframe');
  iframe.style.position = 'fixed';
  iframe.style.top = '0';
  iframe.style.left = '0';
  iframe.style.width = '100vw';
  iframe.style.height = '100vh';
  iframe.style.zIndex = '9999';
  iframe.style.border = 'none';
  iframe.src = 'http://localhost/careercompass/pages/loading.html';

  document.body.appendChild(iframe);

  window.addEventListener('load', function() {
    function checkMediaSrc() {
        const mediaElements = document.querySelectorAll('img:not([data-lazy="true"]), video:not([data-lazy="true"])');
        let allMediaLoaded = true;

        mediaElements.forEach(media => {
            if (!media.complete || media.naturalHeight === 0 || !media.src || media.src === "") {
                allMediaLoaded = false;

                if (!media.src || media.src === "" || media.naturalHeight === 0) {
                    media.addEventListener('load', checkMediaSrc);
                    media.addEventListener('error', checkMediaSrc);
                }
            }
        });

        if (allMediaLoaded) {
            if (iframe) {
                iframe.style.transition = 'opacity 0.5s ease';
                iframe.style.opacity = '0';
                setTimeout(() => {
                    iframe.remove();
                      window.name = "loaded";
                    console.log("Page Fully Loaded!");
                }, 500);
            }
        } else {
            setTimeout(checkMediaSrc, 100);
            window.name = "mediachk";
        }
    }

    checkMediaSrc();
});

})();
