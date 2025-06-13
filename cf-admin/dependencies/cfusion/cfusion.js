import { injectHeader, injectFooter } from './scripts/template.js';
import { injectDependencies } from './scripts/inject_deps.js';
import { loadMedia, checkAllMediaElements } from './scripts/media_handler.js';

loadMedia();
checkAllMediaElements();
injectDependencies('fontawesome', 'cfusion', 'gsap');
console.log("CFusion Initiated!");

const endpoint = document.documentElement.getAttribute('data-endpoint');
if (endpoint === 'cms') {
    // No Injections
} else {
    injectHeader();
    injectFooter();
}

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
