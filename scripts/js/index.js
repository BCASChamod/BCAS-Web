// Wait for GSAP to be fully loaded
function waitForGSAP() {
    return new Promise((resolve) => {
        if (typeof gsap !== 'undefined') {
            resolve();
        } else {
            const checkGSAP = setInterval(() => {
                if (typeof gsap !== 'undefined') {
                    clearInterval(checkGSAP);
                    resolve();
                }
            }, 100);
        }
    });
}

function autoscroll(elementOrId, loop = false, direction = 'down', speed = 1) {
    const element = typeof elementOrId === 'string'
        ? document.getElementById(elementOrId)
        : elementOrId;
    if (!element) return;

    let dir = direction.toLowerCase();
    let rafId;

    function step() {
        let deltaX = 0, deltaY = 0;
        if (dir === 'down') deltaY = speed;
        else if (dir === 'up') deltaY = -speed;
        else if (dir === 'right') deltaX = speed;
        else if (dir === 'left') deltaX = -speed;
        else {
            console.warn('[autoscroll] bad direction:', dir);
            return;
        }

        element.scrollBy(deltaX, deltaY);

        const atBottom = element.scrollTop + element.clientHeight >= element.scrollHeight;
        const atTop = element.scrollTop <= 0;
        const atRight = element.scrollLeft + element.clientWidth >= element.scrollWidth;
        const atLeft = element.scrollLeft <= 0;

        let hitEdge = false;
        if ((dir === 'down' && atBottom) ||
            (dir === 'up' && atTop) ||
            (dir === 'right' && atRight) ||
            (dir === 'left' && atLeft)) {
            hitEdge = true;
        }

        if (hitEdge) {
            if (loop) {
                if (dir === 'down') dir = 'up';
                else if (dir === 'up') dir = 'down';
                else if (dir === 'right') dir = 'left';
                else if (dir === 'left') dir = 'right';
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

// Fixed landing animation function
async function animateLanding() {
    // Wait for GSAP to be available
    await waitForGSAP();
    
    const landingText = document.getElementById('landingText');
    const landingSubs = document.getElementById('landingSubjects');
    
    // Check if elements exist
    if (!landingText || !landingSubs) {
        console.warn('Landing elements not found:', { landingText, landingSubs });
        return;
    }
    
    landingText.style.opacity = '1';
    
    // Remove any existing scroll listeners to prevent duplicates
    const existingHandler = window.landingScrollHandler;
    if (existingHandler) {
        window.removeEventListener('scroll', existingHandler);
    }
    
    // Create new scroll handler
    window.landingScrollHandler = function() {
        const scrollY = window.scrollY || window.pageYOffset;
        const translateY = scrollY * 0.8;

        // Kill any existing animations on these elements
        gsap.killTweensOf([landingText, landingSubs]);

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
    };
    
    window.addEventListener('scroll', window.landingScrollHandler);
}

// Fixed timeline branch animation function
async function tlBranchAnim() {
    // Wait for GSAP to be available
    await waitForGSAP();
    
    const items = document.querySelectorAll('.timeline-item');
    
    if (items.length === 0) {
        console.warn('No timeline items found');
        return;
    }

    function setItemStyles(item) {
        const paths = item.querySelectorAll('path:not(.svg-separator)');
        paths.forEach(path => {
            path.style.transform = 'scaleY(0)';
            path.style.transformOrigin = 'center';
            path.style.opacity = '0.2';
            path.style.transition = 'none';
        });

        const innerTexts = item.querySelectorAll('.svg-innertext');
        if (innerTexts.length > 0) {
            gsap.set(innerTexts, { opacity: 0 });
        }

        const innerSubTexts = item.querySelectorAll('.svg-innersubtext');
        if (innerSubTexts.length > 0) {
            gsap.set(innerSubTexts, { opacity: 0 });
        }

        const separators = item.querySelectorAll('.svg-separator');
        if (separators.length > 0) {
            gsap.set(separators, { opacity: 0 });
        }
    }

    function animItemStyles(item) {
        const innerTexts = item.querySelectorAll('.svg-innertext');
        if (innerTexts.length > 0) {
            gsap.to(innerTexts, { opacity: 1, duration: 0.5 });
        }

        const paths = item.querySelectorAll('path:not(.svg-separator)');
        paths.forEach(path => {
            path.style.transition = 'transform 1s ease-in-out, opacity 1s ease-in-out';
            setTimeout(() => {
                path.style.transform = 'scaleY(1)';
                path.style.opacity = '1';
            }, 500);
        });

        setTimeout(() => {
            const innerSubTexts = item.querySelectorAll('.svg-innersubtext');
            if (innerSubTexts.length > 0) {
                gsap.to(innerSubTexts, { opacity: 1, duration: 0.5 });
            }

            const separators = item.querySelectorAll('.svg-separator');
            if (separators.length > 0) {
                gsap.to(separators, { opacity: 0.5, duration: 0.5 });
            }
        }, 1000);
    }

    items.forEach(item => setItemStyles(item));

    let itemAnimDelay = 0;

    const timelineContainer = document.getElementById('timelineContainer');
    if (!timelineContainer) {
        console.warn('Timeline container not found');
        return;
    }

    const timelineContainerObserver = new IntersectionObserver((entries) => {
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

    timelineContainer.addEventListener('click', () => {
        console.log('Timeline container clicked');
    });

    timelineContainerObserver.observe(timelineContainer);

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

// Initialize timeline scroll
function initializeTimeline() {
    const timelineContainer = document.getElementById('timelineContainer');
    if (timelineContainer) {
        timelineContainer.scrollLeft = 100;
        autoscroll(timelineContainer, true, 'right', 1.2);
    }
}

// Video controls and functionality
function initializeVideoControls() {
    const video = document.getElementById('testimonialVideo');
    const seekbar = document.getElementById('testimonialSeekbar');
    
    if (!video || !seekbar) return;
    
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
}

// Caption and volume controls
function initializeCaptionAndVolumeControls() {
    const video = document.getElementById("testimonialVideo");
    const select = document.getElementById("testCaption");
    const volumeControl = document.getElementById("videoVolume");
    const volumeIndicator = document.getElementById("volumeIndicator");

    if (!video) return;

    // Caption selection
    if (select) {
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
    }

    // Volume control
    if (volumeControl && volumeIndicator) {
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

        video.addEventListener("volumechange", () => {
            updateIcon(video.volume);
        });
    }
}

// Video overlay functionality
function addVideoOverlay() {
    const video = document.querySelector("video");
    if (!video) return;
    
    const videoParent = video.parentElement;

    const existingOverlay = videoParent.querySelector('.testimonial-video-overlay');
    if (existingOverlay) {
        existingOverlay.remove();
    }

    const overlay = document.createElement('div');
    overlay.className = 'testimonial-video-overlay';
    overlay.innerHTML = '<span>Click to replay</span>';

    Object.assign(overlay.style, {
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        fontFamily: '"Font Awesome 6 Pro", "Font Awesome 6 Free", "FontAwesome", sans-serif',
        fontWeight: '900',
        position: 'absolute',
        top: '0',
        left: '0',
        right: '0',
        bottom: '0',
        fontSize: '2rem',
        color: '#fff',
        textShadow: '2px 2px 8px rgba(0,0,0,0.7)',
        background: 'rgba(0,0,0,0.5)',
        backdropFilter: 'blur(10px)',
        cursor: 'pointer',
        zIndex: 10
    });

    if (getComputedStyle(videoParent).position === 'static') {
        videoParent.style.position = 'relative';
    }

    overlay.addEventListener('click', () => {
        video.currentTime = 0;
        video.play();
        removeVideoOverlay();
    });

    videoParent.appendChild(overlay);
}

function removeVideoOverlay() {
    const video = document.querySelector("video");
    if (!video) return;
    
    const videoParent = video.parentElement;
    const overlay = videoParent.querySelector('.testimonial-video-overlay');
    if (overlay) {
        overlay.remove();
    }
}

function initializeVideoOverlay() {
    const video = document.getElementById("testimonialVideo");
    if (!video) return;

    video.addEventListener('ended', addVideoOverlay);
    video.addEventListener('play', removeVideoOverlay);
    video.addEventListener('seeked', () => {
        if (!video.ended) removeVideoOverlay();
    });
}

// Form initialization
function initializeForm() {
    const formArea = document.getElementById('formArea');
    if (!formArea) return;
    
    let formStatus = formArea.getAttribute('data-submission') || 'pre';

    if (formStatus === 'pre') {
        document.querySelector('.form-success')?.classList.add('hidden');
        document.querySelector('.form-pending')?.classList.add('visible');
        formArea.innerHTML = `
            <form>
                <div>
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" />
                </div>
                <div>
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" />
                </div>

                <div class="full-width">
                    <label for="qualification">Your Highest Academic Qualification</label>
                    <select id="qualification">
                        <option value="">Select qualification</option>
                        <option value="OL">O/L</option>
                        <option value="AL">A/L</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Degree">Degree</option>
                        <option value="Masters">Masters</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="full-width">
                    <label for="programme">Interested Programme</label>
                    <select id="programme">
                        <option value="">Select programme</option>
                    </select>
                </div>

                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" />
                </div>
                <div>
                    <label for="branch">Closest Branch</label>
                    <input type="text" id="branch" />
                </div>

                <div class="full-width">
                    <label for="contact">Contact Number</label>
                    <input type="text" id="contact" />
                </div>

                <div class="checkbox-wrapper">
                    <input type="checkbox" id="consent" />
                    <label for="consent"><small>I consent to communications from BCAS Campus. <a href="#" style="color: #007bff;">Learn more</a></small></label>
                </div>

                <div class="form-buttons">
                    <input type="text" id="ip" hidden/>
                    <button type="reset" class="new-form">New Form</button>
                    <button type="submit" class="submit">Submit</button>
                </div>
            </form>
        `;

        const programmeSelect = document.getElementById('programme');
        if (window.productData && Array.isArray(window.productData)) {
            window.productData.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.name;
                programmeSelect.appendChild(option);
            });
        }
    } else {
        document.querySelector('.form-success')?.classList.add('visible');
        document.querySelector('.form-pending')?.classList.add('hidden');
        formArea.innerHTML = `
            <div class="success-message">
                <h1>Inquired Successfully</h1>
                <p>One of our experienced counsellors will be in touch with you soon to guide you through your educational journey</p>
                <form>
                    <div class="form-buttons">
                        <button type="reset" class="new-form">New Form</button>
                    </div>
                </form>
            </div>
        `;
    }
}

// Main initialization function
async function initializeApp() {
    console.log('Initializing app...');
    
    // Initialize non-GSAP functionality immediately
    initializeTimeline();
    initializeVideoControls();
    initializeCaptionAndVolumeControls();
    initializeVideoOverlay();
    initializeForm();
    
    // Initialize GSAP-dependent functionality
    try {
        await waitForGSAP();
        console.log('GSAP loaded, initializing animations...');
        await animateLanding();
        await tlBranchAnim();
        console.log('Animations initialized successfully');
    } catch (error) {
        console.error('Error initializing GSAP animations:', error);
    }
}

// Observer for data-loaded attribute
const loadObserver = new MutationObserver((mutationsList) => {
    for (const mutation of mutationsList) {
        if (
            mutation.type === 'attributes' &&
            mutation.attributeName === 'data-loaded' &&
            document.documentElement.getAttribute('data-loaded') === 'true'
        ) {
            console.log('Data loaded attribute detected');
            initializeApp();
            loadObserver.disconnect();
        }
    }
});

// Start observing
loadObserver.observe(document.documentElement, { 
    attributes: true, 
    attributeFilter: ['data-loaded'] 
});

// Also initialize on DOMContentLoaded as fallback
document.addEventListener("DOMContentLoaded", () => {
    console.log('DOM loaded');
    // Only initialize if data-loaded hasn't been set
    if (document.documentElement.getAttribute('data-loaded') !== 'true') {
        console.log('Initializing on DOMContentLoaded');
        initializeApp();
    }
});

// Additional fallback for when GSAP might load after everything else
window.addEventListener('load', () => {
    console.log('Window loaded');
    // Double-check GSAP animations are working
    if (typeof gsap !== 'undefined') {
        console.log('GSAP is available on window load');
    } else {
        console.warn('GSAP not available on window load');
    }
});