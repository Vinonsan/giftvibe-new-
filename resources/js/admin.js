document.addEventListener('DOMContentLoaded', () => {
    // 1. Sidebar Toggles
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.getElementById('admin-sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            const isExpanded = sidebarToggle.getAttribute('aria-expanded') === 'true';
            sidebarToggle.setAttribute('aria-expanded', !isExpanded);
            sidebar.classList.toggle('-translate-x-full');
        });
    }

    // Sidebar Collapsible Groups
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-sidebar-group-trigger]');
        if (trigger) {
            const menu = trigger.nextElementSibling;
            if (menu) {
                const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                trigger.setAttribute('aria-expanded', !isExpanded);
                menu.classList.toggle('hidden');
                const icon = trigger.querySelector('[data-sidebar-group-icon]');
                if (icon) icon.classList.toggle('rotate-90');
            }
        }
    });

    // 2. Select All Table Checkboxes
    document.addEventListener('change', (e) => {
        const selectAll = e.target.closest('[data-select-all]');
        if (selectAll) {
            const table = selectAll.closest('table');
            if (table) {
                const checkboxes = table.querySelectorAll('tbody input[type="checkbox"]');
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            }
        }
    });

    // 3. User Menu Dropdown in Topbar
    const userMenuTrigger = document.querySelector('[data-user-menu-trigger]');
    const userMenu = document.querySelector('[data-user-menu]');
    if (userMenuTrigger && userMenu) {
        userMenuTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isExpanded = userMenuTrigger.getAttribute('aria-expanded') === 'true';
            userMenuTrigger.setAttribute('aria-expanded', !isExpanded);
            userMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target) && !userMenuTrigger.contains(e.target)) {
                userMenu.classList.add('hidden');
                userMenuTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // 4. File/Image Preview Handler
    document.addEventListener('change', (e) => {
        const input = e.target.closest('[data-image-preview-input]');
        if (input && input.files && input.files[0]) {
            const container = input.closest('[data-image-preview-container]');
            if (container) {
                const preview = container.querySelector('[data-image-preview]');
                if (preview) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        preview.src = event.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        }
    });

    // 5. Toast / Flash Dismissals
    document.addEventListener('click', (e) => {
        const dismissBtn = e.target.closest('[data-toast-dismiss]');
        if (dismissBtn) {
            const toast = dismissBtn.closest('[data-toast]');
            if (toast) {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }
    });
});
