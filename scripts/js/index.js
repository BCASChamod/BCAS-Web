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
