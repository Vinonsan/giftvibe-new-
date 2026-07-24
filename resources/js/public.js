document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Control
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');
        });
    }

    // 2. Dropdowns Handling (using event delegation)
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-dropdown-trigger]');
        if (trigger) {
            const container = trigger.closest('[data-dropdown]');
            if (container) {
                const menu = container.querySelector('[data-dropdown-menu]');
                if (menu) {
                    const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                    trigger.setAttribute('aria-expanded', !isExpanded);
                    menu.classList.toggle('hidden');
                }
            }
        } else {
            // Click outside dropdowns closes them
            document.querySelectorAll('[data-dropdown]').forEach(dropdown => {
                const menu = dropdown.querySelector('[data-dropdown-menu]');
                const triggerBtn = dropdown.querySelector('[data-dropdown-trigger]');
                if (menu && !menu.classList.contains('hidden') && !dropdown.contains(e.target)) {
                    menu.classList.add('hidden');
                    if (triggerBtn) triggerBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });

    // 3. Modals and Drawers
    let activeModal = null;
    let focusBackup = null;

    window.openModal = function(modalId) {
        const modal = document.querySelector(modalId);
        if (!modal) return;
        focusBackup = document.activeElement;
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        activeModal = modal;
        // Focus first interactive element inside the modal
        const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex="0"]');
        if (focusable.length > 0) focusable[0].focus();
    };

    window.closeModal = function(modalId) {
        const modal = document.querySelector(modalId);
        if (!modal) return;
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        if (activeModal === modal) activeModal = null;
        if (focusBackup) {
            focusBackup.focus();
            focusBackup = null;
        }
    };

    window.toggleDrawer = function(drawerId) {
        const drawer = document.querySelector(drawerId);
        if (!drawer) return;
        const isOpen = drawer.classList.contains('translate-x-0');
        if (isOpen) {
            drawer.classList.remove('translate-x-0');
            drawer.classList.add('translate-x-full');
        } else {
            drawer.classList.remove('translate-x-full');
            drawer.classList.add('translate-x-0');
        }
    };

    // Close on overlay clicks
    document.addEventListener('click', (e) => {
        if (e.target.hasAttribute('data-modal-overlay')) {
            const modal = e.target.closest('[data-modal]');
            if (modal) closeModal('#' + modal.id);
        }
        if (e.target.hasAttribute('data-drawer-overlay')) {
            const drawer = e.target.closest('[data-drawer]');
            if (drawer) {
                drawer.classList.remove('translate-x-0');
                drawer.classList.add('translate-x-full');
            }
        }
    });

    // Accessibility keyboard helpers
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (activeModal) {
                closeModal('#' + activeModal.id);
            }
            // Close mobile menu
            if (menuToggle && mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.focus();
            }
            // Close all open drawers
            document.querySelectorAll('[data-drawer]').forEach(drawer => {
                if (drawer.classList.contains('translate-x-0')) {
                    drawer.classList.remove('translate-x-0');
                    drawer.classList.add('translate-x-full');
                }
            });
        }
    });

    // 4. Accordions
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-accordion-trigger]');
        if (trigger) {
            const contentId = trigger.getAttribute('aria-controls');
            const content = document.getElementById(contentId);
            if (content) {
                const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                trigger.setAttribute('aria-expanded', !isExpanded);
                content.classList.toggle('hidden');
                const icon = trigger.querySelector('[data-accordion-icon]');
                if (icon) icon.classList.toggle('rotate-180');
            }
        }
    });

    // 5. Tabs
    document.addEventListener('click', (e) => {
        const tabBtn = e.target.closest('[data-tab]');
        if (tabBtn) {
            const list = tabBtn.closest('[role="tablist"]');
            const targetId = tabBtn.getAttribute('aria-controls');
            if (list) {
                list.querySelectorAll('[data-tab]').forEach(btn => {
                    btn.setAttribute('aria-selected', 'false');
                    btn.classList.remove('border-primary-500', 'text-primary-600');
                    btn.classList.add('border-transparent', 'text-slate-500');
                });
                tabBtn.setAttribute('aria-selected', 'true');
                tabBtn.classList.add('border-primary-500', 'text-primary-600');
                tabBtn.classList.remove('border-transparent', 'text-slate-500');

                const container = list.parentElement;
                container.querySelectorAll('[role="tabpanel"]').forEach(panel => {
                    panel.classList.add('hidden');
                });
                const activePanel = document.getElementById(targetId);
                if (activePanel) activePanel.classList.remove('hidden');
            }
        }
    });
});
