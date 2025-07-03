// Global media data cache
let mediaDataCache = {};

// Export so you can import elsewhere
export async function loadMedia() {
  try {
    // 1) Grab media data and cache it
    const res = await fetch('http://localhost/bcas-web/cf-admin/server/scripts/php/mediahandler.php');
    mediaDataCache = await res.json();

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
      const data = mediaDataCache[id];

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

    // 4) Start the ID change observer
    startIdChangeObserver();
  }
  catch(err) {
    console.error('❌ Error in loadMedia():', err);
    // Optional: retry logic here with backoff
  }
}

// Function to update media element with new data
async function updateMediaElement(el, newId) {
  try {
    // If we don't have cached data for this ID, fetch fresh data
    if (!mediaDataCache[newId]) {
      console.log(`🔄 Fetching fresh data for new ID: ${newId}`);
      const res = await fetch('http://localhost/bcas-web/cf-admin/server/scripts/php/mediahandler.php');
      const freshData = await res.json();
      mediaDataCache = { ...mediaDataCache, ...freshData };
    }

    const data = mediaDataCache[newId];
    
    if (data && data.is_active) {
      console.log(`✨ Updating element with ID: ${newId}`);
      
      if (el.tagName === 'VIDEO') {
        el.src = data.src;
        if (data.srcset) {
          const source = el.querySelector('source');
          source?.setAttribute('srcset', data.srcset);
        }
        el.load(); // Reload video with new source
      } else {
        el.src = data.src;
        if (data.srcset) el.srcset = data.srcset;
      }
      el.alt = data.alt || '';
      el.classList.remove('placeholder');
    } else {
      console.log(`⚠️ No active data found for ID: ${newId}, using placeholder`);
      el.classList.add('placeholder');
      el.src = '/careercompass/resources/img/placeholder.webp';
      el.srcset = '';
      el.alt = 'Placeholder';
    }
  } catch (err) {
    console.error(`❌ Error updating element with ID ${newId}:`, err);
  }
}

// Persistent observer for ID changes
let idChangeObserver = null;

function startIdChangeObserver() {
  // Clean up existing observer if any
  if (idChangeObserver) {
    idChangeObserver.disconnect();
  }

  console.log('🔍 Starting ID change observer for media elements');

  idChangeObserver = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      // Check for attribute changes
      if (mutation.type === 'attributes' && mutation.attributeName === 'id') {
        const el = mutation.target;
        
        // Only process img and video elements
        if (el.tagName === 'IMG' || el.tagName === 'VIDEO') {
          const newId = el.id;
          const oldId = mutation.oldValue;
          
          if (newId && newId !== oldId) {
            console.log(`🔄 ID changed from "${oldId}" to "${newId}" on ${el.tagName}`);
            updateMediaElement(el, newId);
          }
        }
      }
      
      // Check for newly added nodes
      if (mutation.type === 'childList') {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === Node.ELEMENT_NODE) {
            // Check if the added node itself is an img/video with an ID
            if ((node.tagName === 'IMG' || node.tagName === 'VIDEO') && node.id) {
              console.log(`➕ New ${node.tagName} element added with ID: ${node.id}`);
              updateMediaElement(node, node.id);
            }
            
            // Check for img/video elements within the added node
            const mediaEls = node.querySelectorAll?.('img[id], video[id]');
            mediaEls?.forEach(el => {
              console.log(`➕ New ${el.tagName} element found with ID: ${el.id}`);
              updateMediaElement(el, el.id);
            });
          }
        });
      }
    });
  });

  // Start observing the entire document
  idChangeObserver.observe(document.body, {
    childList: true,
    subtree: true,
    attributes: true,
    attributeOldValue: true,
    attributeFilter: ['id']
  });
}

// Function to refresh media data cache
export async function refreshMediaCache() {
  try {
    console.log('🔄 Refreshing media data cache...');
    const res = await fetch('http://localhost/bcas-web/cf-admin/server/scripts/php/mediahandler.php');
    mediaDataCache = await res.json();
    console.log('✅ Media data cache refreshed');
  } catch (err) {
    console.error('❌ Error refreshing media cache:', err);
  }
}

// Function to stop the observer (useful for cleanup)
export function stopIdChangeObserver() {
  if (idChangeObserver) {
    idChangeObserver.disconnect();
    idChangeObserver = null;
    console.log('🛑 ID change observer stopped');
  }
}

// Function to manually trigger update for a specific element
export function updateElementById(elementId) {
  const el = document.getElementById(elementId);
  if (el && (el.tagName === 'IMG' || el.tagName === 'VIDEO')) {
    updateMediaElement(el, elementId);
  } else {
    console.warn(`Element with ID "${elementId}" not found or is not an img/video element`);
  }
}