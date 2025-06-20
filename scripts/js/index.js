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

function animateLanding() {
        const landingText = document.getElementById('landingText');
        const landingSubs = document.getElementById('landingSubjects');
        landingText.style.opacity = '1';
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY || window.pageYOffset;
            const translateY = scrollY * 0.8;
            
            gsap.to(landingText, {
                y: translateY,
                ease: "power1.out",
                duration: 0.3,
                overwrite: 'auto'
            });

            gsap.to(landingSubs, {
                scale: 1 + scrollY / 2000,
                ease: "power1.out",
                duration: 0.3,
                overwrite: 'auto'
            });
        });
}

  const video = document.getElementById('testimonialVideo');
          const seekbar = document.getElementById('testimonialSeekbar');
          let seeking = false;

          video.addEventListener('loadedmetadata', function() {
            seekbar.max = video.duration;
          });

          video.addEventListener('timeupdate', function() {
            if (!seeking) {
              seekbar.value = video.currentTime;
            }
          });

          seekbar.addEventListener('input', function() {
            seeking = true;
            video.currentTime = seekbar.value;
          });

          seekbar.addEventListener('change', function() {
            seeking = false;
          });

          function updateSeekbar() {
            if (!seeking && !video.paused && !video.ended) {
              seekbar.value = video.currentTime;
            }
            requestAnimationFrame(updateSeekbar);
          }
          updateSeekbar();

function tlBranchAnim() {
    const items = document.querySelectorAll('.timeline-item');

    function setItemStyles(item) {
        const paths = item.querySelectorAll('path:not(.svg-separator)');
        paths.forEach(path => {
            path.style.transform = 'scaleY(0)';
            path.style.transformOrigin = 'center';
            path.style.opacity = '0.2';
            path.style.transition = 'none';
        });

        const innerTexts = item.querySelectorAll('.svg-innertext');
        innerTexts.forEach(el => {
            gsap.set(el, { opacity: 0 });
        });

        const innerSubTexts = item.querySelectorAll('.svg-innersubtext');
        innerSubTexts.forEach(el => {
            gsap.set(el, { opacity: 0 });
        });

        const separators = item.querySelectorAll('.svg-separator');
        separators.forEach(sep => {
            gsap.set(sep, { opacity: 0 });
        });
    }

    function animItemStyles(item) {
            const innerTexts = item.querySelectorAll('.svg-innertext');
            gsap.to(innerTexts, { opacity: 1, duration: 0.5 });

            const paths = item.querySelectorAll('path:not(.svg-separator)');
            paths.forEach(path => {
                path.style.transition = 'transform 1s ease-in-out, opacity 1s ease-in-out';
                setTimeout(() => {
                    path.style.transform = 'scaleY(1)';
                    path.style.opacity = '1';
                }, 0.5);
            });

            setTimeout(() => {
                const innerSubTexts = item.querySelectorAll('.svg-innersubtext');
                gsap.to(innerSubTexts, { opacity: 1, duration: 0.5 });

                const separators = item.querySelectorAll('.svg-separator');
                gsap.to(separators, { opacity: 0.5, duration: 0.5 });
            }, 1000);
    }

    items.forEach(item => setItemStyles(item));

    let itemAnimDelay = 0;

    const timelinecontainerobserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    itemAnimDelay = 3000;
                }, 1000);
            } else {
                itemAnimDelay = 0;
            }
        });
    });

    const timelineContainer = document.getElementById('timelineContainer');
    timelineContainer.addEventListener('click', () => {
        console.log('Timeline container mouse enter');
    });

    timelinecontainerobserver.observe(timelineContainer);

    const timelineObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (!entry.target.classList.contains('active-tmbranch')) {
                        entry.target.classList.add('active-tmbranch');
                        setTimeout(() => {
                            animItemStyles(entry.target);
                        }, itemAnimDelay);

                }
            } else {
                entry.target.classList.remove('active-tmbranch');
                setItemStyles(entry.target);
            }
        });
    }, { threshold: 0.1 });

    items.forEach(item => timelineObserver.observe(item));
}

const aiobserver = new MutationObserver((mutationsList) => {
    for (const mutation of mutationsList) {
        if (
            mutation.type === 'attributes' &&
            mutation.attributeName === 'data-loaded' &&
            document.documentElement.getAttribute('data-loaded') === 'true'
        ) {
            animateLanding();
            tlBranchAnim();
            aiobserver.disconnect();
        }
    }
});

aiobserver.observe(document.documentElement, { attributes: true, attributeFilter: ['data-loaded'] });



document.addEventListener("DOMContentLoaded", () => {
    const video   = document.getElementById("testimonialVideo");
    const select  = document.getElementById("testCaption");
    const volumeControl = document.getElementById("videoVolume");
    const volumeIndicator = document.getElementById("volumeIndicator");

    // Caption selection
    Array.from(video.textTracks).forEach(track => {
        track.mode = "disabled";
    });

    select.addEventListener("change", () => {
        const lang = select.value;
        Array.from(video.textTracks).forEach(track => {
            if (track.language === lang) {
                track.mode = "showing";
            } else {
                track.mode = "disabled";
            }
        });
    });

    // Volume control
    video.muted = true;
    video.volume = 0;
    updateIcon(0);

    volumeControl.addEventListener("input", () => {
        const vol = parseFloat(volumeControl.value);
        video.volume = vol;
        video.muted = vol === 0;
        updateIcon(vol);
    });

    function updateIcon(vol) {
        if (vol === 0) {
            volumeIndicator.classList.remove("fa-volume", "fa-volume-low", "fa-volume-high");
            volumeIndicator.classList.add("fa-volume-xmark");
        } else if (vol > 0 && vol <= 0.5) {
            volumeIndicator.classList.remove("fa-volume-xmark", "fa-volume-high");
            volumeIndicator.classList.add("fa-volume-low");
            volumeIndicator.classList.remove("fa-volume");
        } else {
            volumeIndicator.classList.remove("fa-volume-xmark", "fa-volume-low");
            volumeIndicator.classList.add("fa-volume-high");
            volumeIndicator.classList.remove("fa-volume");
        }
    }

    // Update icon on slider move (for immediate feedback)
    volumeControl.addEventListener("input", () => {
        const vol = parseFloat(volumeControl.value);
        video.volume = vol;
        video.muted = vol === 0;
        updateIcon(vol);
    });

    // Also update icon if volume is changed programmatically
    video.addEventListener("volumechange", () => {
        updateIcon(video.volume);
    });
});
