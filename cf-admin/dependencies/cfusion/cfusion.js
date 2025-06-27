import { initializeTheme, setupThemeSwitch } from './scripts/theme.js';
import { injectHeader, injectFooter } from './scripts/template.js';
import { injectDependencies } from './scripts/inject_deps.js';
import { loadMedia } from './scripts/media_handler.js';
import { loadingOverlay } from './scripts/loading.js';

initializeTheme();
// setupThemeSwitch(document.getElementById('themeSwitch'));
loadMedia();
injectDependencies('fontawesome', 'cfusion', 'gsap', 'bootstrapGrid');
loadingOverlay();
console.log("CFusion Initiated!");

const side = document.documentElement.getAttribute('data-side');
if (side === 'client') {
    // No Injections
} else { 
    injectHeader();
    injectFooter();
}

    document.addEventListener("DOMContentLoaded", () => {


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


