function waitForGSAPAndScrollTrigger() {
    return new Promise((resolve) => {
        const check = setInterval(() => {
            if (typeof window.gsap !== 'undefined' && typeof window.ScrollTrigger !== 'undefined') {
                clearInterval(check);
                resolve();
            }
        }, 100);
    });
}

class ScrollAnimationFramework {
    constructor(options = {}) {
        this.options = {
            duration: 1,
            ease: "power2.out",
            threshold: 0.1,
            ...options
        };

        waitForGSAPAndScrollTrigger().then(() => this.init());
    }

    init() {
        window.gsap.registerPlugin(window.ScrollTrigger);
        this.setupAnimations();
    }
    

    setupAnimations() {

        const style = document.createElement('style');
        style.innerHTML = `
        .scroll-fade-in { opacity: 0; }
        .scroll-fade-up { opacity: 0; transform: translateY(50px); }
        .scroll-fade-down { opacity: 0; transform: translateY(-50px); }
        .scroll-fade-left { opacity: 0; transform: translateX(-50px); }
        .scroll-fade-right { opacity: 0; transform: translateX(50px); }
        .scroll-scale { opacity: 0; transform: scale(0.8); }
        .scroll-rotate { opacity: 0; transform: rotate(15deg); }
        .scroll-skew { opacity: 0; transform: skewX(15deg); }
        `;
        document.head.appendChild(style);

        this.createAnimation('.scroll-fade-in', { opacity: 1 });
        this.createAnimation('.scroll-fade-up', { opacity: 1, y: 0 });
        this.createAnimation('.scroll-fade-down', { opacity: 1, y: 0 });
        this.createAnimation('.scroll-fade-left', { opacity: 1, x: 0 });
        this.createAnimation('.scroll-fade-right', { opacity: 1, x: 0 });
        this.createAnimation('.scroll-scale', { opacity: 1, scale: 1 });
        this.createAnimation('.scroll-rotate', { opacity: 1, rotation: 0 });
        this.createAnimation('.scroll-skew', { opacity: 1, skewX: 0 });

        this.setupProgressBars();
        this.setupCounters();
    }

    createAnimation(selector, toVars) {
        const elements = document.querySelectorAll(selector);

        elements.forEach(element => {
            const delay = parseFloat(element.dataset.delay) || 0;
            const duration = parseFloat(element.dataset.duration) || this.options.duration;
            const ease = element.dataset.ease || this.options.ease;

            window.gsap.to(element, {
                ...toVars,
                duration: duration,
                ease: ease,
                delay: delay,
                scrollTrigger: {
                    trigger: element,
                    start: "top 80%",
                    end: "bottom 20%",
                    toggleActions: "play none none reverse"
                }
            });
        });
    }

    setupProgressBars() {
        const progressBars = document.querySelectorAll('.scroll-progress');

        progressBars.forEach(bar => {
            const progress = parseInt(bar.dataset.progress) || 100;

            window.gsap.to(bar, {
                x: `${progress - 100}%`,
                duration: 1.5,
                ease: "power2.out",
                scrollTrigger: {
                    trigger: bar,
                    start: "top 80%",
                    toggleActions: "play none none reverse"
                }
            });
        });
    }

    setupCounters() {
        const counters = document.querySelectorAll('.scroll-counter');

        counters.forEach(counter => {
            const target = parseInt(counter.dataset.target) || 0;

            window.gsap.to(counter, {
                innerText: target,
                duration: 2,
                ease: "power2.out",
                snap: { innerText: 1 },
                scrollTrigger: {
                    trigger: counter,
                    start: "top 80%",
                    toggleActions: "play none none reverse"
                }
            });
        });
    }

    animateElement(element, animation = 'fade-up', options = {}) {
        const animationMap = {
            'fade-in': { opacity: 1 },
            'fade-up': { opacity: 1, y: 0 },
            'fade-down': { opacity: 1, y: 0 },
            'fade-left': { opacity: 1, x: 0 },
            'fade-right': { opacity: 1, x: 0 },
            'scale': { opacity: 1, scale: 1 },
            'rotate': { opacity: 1, rotation: 0 },
            'skew': { opacity: 1, skewX: 0 }
        };

        const toVars = animationMap[animation] || animationMap['fade-up'];

        window.gsap.to(element, {
            ...toVars,
            duration: options.duration || this.options.duration,
            ease: options.ease || this.options.ease,
            delay: options.delay || 0,
            scrollTrigger: {
                trigger: element,
                start: options.start || "top 80%",
                end: options.end || "bottom 20%",
                toggleActions: options.toggleActions || "play none none reverse"
            }
        });
    }

    refresh() {
        if (typeof window.ScrollTrigger !== 'undefined') {
            window.ScrollTrigger.refresh();
        }
    }

    destroy() {
        if (typeof window.ScrollTrigger !== 'undefined') {
            window.ScrollTrigger.getAll().forEach(trigger => trigger.kill());
        }
    }
}

export function autoscroll(elementOrId, loop = false, direction = 'down', speed = 1) {
    const element = typeof elementOrId === 'string'
        ? document.getElementById(elementOrId)
        : elementOrId;
    let rafId;

    if (!element) {
        return {
            stop() {
                if (rafId) cancelAnimationFrame(rafId);
            }
        };
    }

    let dir = direction.toLowerCase();

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

export default ScrollAnimationFramework;
