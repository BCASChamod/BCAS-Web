import { injectHeader, injectFooter } from './scripts/template.js';
import { injectDependencies } from './scripts/inject_deps.js';

injectDependencies('fontawesome', 'cfusion', 'gsap');
console.log("CFusion Initiated!");

document.addEventListener("DOMContentLoaded", () => {

    const lazyElements = document.querySelectorAll("[data-lazy='true']");
    
    lazyElements.forEach(element => {
        const isVideo = element.tagName.toLowerCase() === "video";
        const source = isVideo ? element.querySelector("source") : element;
        const mediaSrc = source.getAttribute("data-src");
    
        const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
            if (isVideo) {
                source.setAttribute("src", mediaSrc);
                element.load();
            } else {
                source.setAttribute("src", mediaSrc);
            }
            observer.disconnect();
            }
        });
        });
    
        observer.observe(element);
    });
    
    let CurrentTheme = "";

    const forms = document.querySelectorAll('form');

    if (forms.length > 0) {
        forms.forEach(form => {
            const formInputs = form.querySelectorAll('input[required]');
            const formSubBtn = form.querySelector('button[type="submit"]');

            formInputs.forEach(input => {
                input.addEventListener('input', () => {
                    let isAnyEmpty = false;

                    formInputs.forEach(inp => {
                        if (inp.value.trim() === '') {
                            isAnyEmpty = true;
                        }
                    });

                    console.log('status:', isAnyEmpty);

                    if (isAnyEmpty) {
                        formSubBtn.setAttribute('disabled', '');
                    } else {
                        formSubBtn.removeAttribute('disabled');
                    }
                });
            });
        });
    }


    function loadMedia() {
        fetch('http://localhost/careercompass/cf_admin/scripts/mediahandler.php')
            .then(response => response.json())
            .then(mediaData => {
                const mediaElements = document.querySelectorAll('img, video');
    
                mediaElements.forEach(element => {
                    const mediaItem = mediaData[element.id];
                    if (mediaItem) {
                        if (mediaItem.is_active) {
                            element.src = mediaItem.src;
                            element.alt = mediaItem.alt;
                            if (mediaItem.srcset) {
                                element.srcset = mediaItem.srcset;
                            }
                        } else {
                            console.log(`Element ID ${element.id} is not loaded with an image cause its disabled! Re-enable it or change it.`);
                            element.id = 'placeholder_img';
                            element.src = 'http://localhost/careercompass/resources/img/placeholder.webp';
                            element.alt = 'Placeholder: Image is disabled!';
                            if (element.srcset) {
                                element.srcset = '';
                            }
                        }
                    }
                });
    
                checkAllMediaElements();
            })
            .catch(error => {
                console.error("Error fetching media data:", error);
            });
    }
    
    function checkAllMediaElements() {
        console.log("Media checking...");
        // Select all img and video elements that do not have the data-lazy attribute set to true
        const mediaElements = document.querySelectorAll('img:not([data-lazy="true"]), video:not([data-lazy="true"])');

        let allLoaded = true;

        // Iterate over each media element
        mediaElements.forEach(element => {
            // Check if the element does not have the data-lazy attribute and its src is not set
            if (!element.hasAttribute('data-lazy') && !element.src) {
                allLoaded = false;
                loadMedia();  // Re-load media if any element is missing src
                console.log("Media Element is not loaded!");
            }
        });

        // If not all media elements are loaded, check again after 1 second
        if (!allLoaded) {
            setTimeout(checkAllMediaElements, 1000);  // Check again after 1 second
        }
    }
    
    // Initial call to load media data and set src attributes
    loadMedia();

    // MutationObserver to detect new img or video elements added to the DOM
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(node => {
                    if (node.tagName === 'IMG' || node.tagName === 'VIDEO') {
                        loadMedia();  // Re-run loadMedia when new img or video elements are added
                    }
                });
            }
        }
    });

    // Start observing the document for changes
    observer.observe(document.body, { childList: true, subtree: true });
    
});
