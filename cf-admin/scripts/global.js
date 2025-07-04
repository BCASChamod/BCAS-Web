class ModalHandler {
    constructor() {
        this.overlay = document.getElementById('overlay');
        this.activeModals = new Set();
        this.animationDuration = 300;
        
        this.init();
    }

    init() {
        if (!this.overlay) {
            this.createOverlay();
        }

        this.setupEventListeners();
        
        this.initializeModals();
    }

    createOverlay() {
        this.overlay = document.createElement('div');
        this.overlay.id = 'overlay';
        this.overlay.className = 'modal-overlay';
        this.overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 95;
            opacity: 0;
            visibility: hidden;
            transition: opacity ${this.animationDuration}ms ease, visibility ${this.animationDuration}ms ease;
        `;
        document.body.appendChild(this.overlay);
    }

    setupEventListeners() {
        // Close modal when clicking overlay
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.closeAllModals();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.activeModals.size > 0) {
                this.closeAllModals();
            }
        });

        // Handle modal triggers
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-modal-open]');
            if (trigger) {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal-open');
                this.openModal(modalId);
            }

            const closeBtn = e.target.closest('[data-modal-close]');
            if (closeBtn) {
                e.preventDefault();
                const modalId = closeBtn.getAttribute('data-modal-close');
                if (modalId) {
                    this.closeModal(modalId);
                } else {
                    this.closeAllModals();
                }
            }
        });
    }

    initializeModals() {
        const modals = document.querySelectorAll('.modal, [data-modal]');
        modals.forEach(modal => {
            if (!modal.style.transition) {
                modal.style.transition = `opacity ${this.animationDuration}ms ease, transform ${this.animationDuration}ms ease`;
            }
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.9)';
            modal.style.visibility = 'hidden';
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId) || document.querySelector(`[data-modal="${modalId}"]`);
        
        if (!modal) {
            console.warn(`Modal with ID "${modalId}" not found`);
            return;
        }

        this.activeModals.add(modalId);
        this.showOverlay();
        modal.classList.add('modal-active');
        modal.style.visibility = 'visible';
        requestAnimationFrame(() => {
            modal.style.opacity = '1';
            modal.style.transform = 'translate(-50%, -50%) scale(1)';
        });

        document.body.style.overflow = 'hidden';
        this.dispatchEvent('modalOpened', { modalId, modal });
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId) || document.querySelector(`[data-modal="${modalId}"]`);
        
        if (!modal || !this.activeModals.has(modalId)) {
            return;
        }
        modal.style.opacity = '0';
        modal.style.transform = 'translate(-50%, -50%) scale(0.9)';

        setTimeout(() => {
            modal.classList.remove('modal-active');
            modal.style.visibility = 'hidden';
            this.activeModals.delete(modalId);

            if (this.activeModals.size === 0) {
                this.hideOverlay();
                document.body.style.overflow = '';
            }
            this.dispatchEvent('modalClosed', { modalId, modal });
        }, this.animationDuration);
    }

    closeAllModals() {
        const modalsToClose = Array.from(this.activeModals);
        modalsToClose.forEach(modalId => this.closeModal(modalId));
    }

    showOverlay() {
        this.overlay.style.visibility = 'visible';
        requestAnimationFrame(() => {
            this.overlay.style.opacity = '1';
        });
    }

    hideOverlay() {
        this.overlay.style.opacity = '0';
        setTimeout(() => {
            this.overlay.style.visibility = 'hidden';
        }, this.animationDuration);
    }

    isModalOpen(modalId) {
        return this.activeModals.has(modalId);
    }

    getActiveModals() {
        return Array.from(this.activeModals);
    }

    dispatchEvent(eventName, detail) {
        const event = new CustomEvent(eventName, { detail });
        document.dispatchEvent(event);
    }

    toggleModal(modalId) {
        if (this.isModalOpen(modalId)) {
            this.closeModal(modalId);
        } else {
            this.openModal(modalId);
        }
    }

    setAnimationDuration(duration) {
        this.animationDuration = duration;
        this.overlay.style.transition = `opacity ${duration}ms ease, visibility ${duration}ms ease`;
        
        const modals = document.querySelectorAll('[data-modal]');
        modals.forEach(modal => {
            modal.style.transition = `opacity ${duration}ms ease, transform ${duration}ms ease`;
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.modalHandler = new ModalHandler();
});
