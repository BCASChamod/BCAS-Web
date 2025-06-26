// Export so you can import elsewhere
export async function loadMedia() {
  try {
    // 1) Grab media data
    const res = await fetch('http://localhost/bcas-web/cf-admin/server/scripts/php/mediahandler.php');
    const mediaData = await res.json();

    // 2) Eager elements: img/video without data-lazy="true"
    const eagerEls = Array.from(
      document.querySelectorAll('img:not([data-lazy="true"]), video:not([data-lazy="true"])')
    );

    let pending = eagerEls.length;
    const checkDone = () => {
      if (--pending <= 0) {
        console.log('✅ All eager media loaded or errored');
      }
    };

    eagerEls.forEach(el => {
      const id = el.id;
      const data = mediaData[id];

      if (data && data.is_active) {
        // wire up src/srcset/alt
        if (el.tagName === 'VIDEO') {
          el.src = data.src;
          if (data.srcset) el.querySelector('source')?.setAttribute('srcset', data.srcset);
        } else {
          el.src = data.src;
          if (data.srcset) el.srcset = data.srcset;
        }
        el.alt = data.alt || '';
      } else {
        // disabled → placeholder
        console.log(`⚠️ Element "${id}" is disabled; using placeholder.`);
        el.classList.add('placeholder');
        el.src = '/careercompass/resources/img/placeholder.webp';
      }

      // wait for each to finish
      el.addEventListener('load',  checkDone, { once: true });
      el.addEventListener('error', checkDone, { once: true });
    });

    if (pending === 0) {
      console.log('No eager media to load.');
    }

    // 3) Lazy elements: watch for visibility
    const lazyEls = document.querySelectorAll('[data-lazy="true"]');
    const io = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;

        const el = entry.target;
        const isVideo = el.tagName.toLowerCase() === 'video';
        const srcAttr = el.getAttribute('data-src');
        if (!srcAttr) return;

        if (isVideo) {
          // set <source> and trigger load()
          const source = el.querySelector('source');
          source?.setAttribute('src', srcAttr);
          el.load();
        } else {
          el.src = srcAttr;
        }

        // cleanup
        observer.unobserve(el);
      });
    }, { rootMargin: '100px' }); // preload a bit before fully in view

    lazyEls.forEach(el => io.observe(el));
  }
  catch(err) {
    console.error('❌ Error in loadMedia():', err);
    // Optional: retry logic here with backoff
  }
}
