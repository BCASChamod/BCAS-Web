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

const aiobserver = new MutationObserver((mutationsList) => {
    for (const mutation of mutationsList) {
        if (
            mutation.type === 'attributes' &&
            mutation.attributeName === 'data-loaded' &&
            document.documentElement.getAttribute('data-loaded') === 'true'
        ) {
            animateLanding();
            aiobserver.disconnect();
        }
    }
});

aiobserver.observe(document.documentElement, { attributes: true, attributeFilter: ['data-loaded'] });
