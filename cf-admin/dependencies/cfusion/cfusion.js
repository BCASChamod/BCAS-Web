import { initializeTheme, setupThemeSwitch } from './scripts/theme.js';
import { injectHeader, injectFooter } from './scripts/template.js';
import { injectDependencies } from './scripts/inject_deps.js';
import { loadMedia, checkAllMediaElements } from './scripts/media_handler.js';
import { showLoadingOverlay } from './scripts/loading.js';

initializeTheme();
// setupThemeSwitch(document.getElementById('themeSwitch'));
loadMedia();
checkAllMediaElements();
injectDependencies('fontawesome', 'cfusion', 'gsap', 'bootstrapGrid');
showLoadingOverlay();
console.log("CFusion Initiated!");

const side = document.documentElement.getAttribute('data-side');
if (side === 'client') {
    // No Injections
} else {
    injectHeader();
    injectFooter();
}

    document.addEventListener("DOMContentLoaded", () => {
        const lazyElements = document.querySelectorAll("[data-lazy='true']");

        lazyElements.forEach(element => {
            const isVideo = element.tagName.toLowerCase() === "video";

            // If it's a video, get the data-src from the <video> tag instead
            const mediaSrc = isVideo 
                ? element.getAttribute("data-src") 
                : element.getAttribute("data-src");

            const source = isVideo ? element.querySelector("source") : element;

            if (!mediaSrc) return; // Skip if there's no data-src

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        if (isVideo) {
                            source.setAttribute("src", mediaSrc);
                            element.load();
                        } else {
                            element.setAttribute("src", mediaSrc);
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

    loadMedia();


    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(node => {
                    if (node.tagName === 'IMG' || node.tagName === 'VIDEO') {
                        loadMedia();
                    }
                });
            }
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
});
