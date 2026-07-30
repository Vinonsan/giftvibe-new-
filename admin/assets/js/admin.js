document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Sidebar Drawer
    const sidebarOpen = document.querySelector('[data-sidebar-open]');
    const sidebarClose = document.querySelector('[data-sidebar-close]');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');

    const setSidebarOpen = (isOpen) => {
        if (!mobileSidebar || !sidebarOverlay) return;

        mobileSidebar.classList.toggle('-translate-x-full', !isOpen);
        mobileSidebar.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        sidebarOverlay.classList.toggle('pointer-events-none', !isOpen);
        sidebarOverlay.classList.toggle('opacity-0', !isOpen);
        sidebarOverlay.classList.toggle('bg-slate-950/0', !isOpen);
        sidebarOverlay.classList.toggle('opacity-100', isOpen);
        sidebarOverlay.classList.toggle('bg-slate-950/55', isOpen);
        document.body.classList.toggle('overflow-hidden', isOpen);
        if (sidebarOpen) {
            sidebarOpen.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }
        if (isOpen && sidebarClose) {
            sidebarClose.focus();
        }
    };

    if (sidebarOpen) sidebarOpen.addEventListener('click', () => setSidebarOpen(true));
    if (sidebarClose) sidebarClose.addEventListener('click', () => setSidebarOpen(false));
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', () => setSidebarOpen(false));

    // 2. Desktop Sidebar Toggle (collapse / expand)
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const adminSidebar = document.getElementById('admin-sidebar');
    const adminShell = document.querySelector('[data-admin-shell]');
    const sidebarStorageKey = 'giftvibe-admin-sidebar-collapsed';

    const setSidebarCollapsed = (isCollapsed) => {
        if (!adminSidebar) return;
        adminSidebar.classList.toggle('collapsed', isCollapsed);
        adminShell?.classList.toggle('sidebar-collapsed', isCollapsed);
        if (sidebarToggle) {
            sidebarToggle.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
            sidebarToggle.setAttribute('title', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
            sidebarToggle.querySelector('[data-sidebar-toggle-icon]')?.classList.toggle('rotate-180', isCollapsed);
            const toggleLabel = sidebarToggle.querySelector('[data-sidebar-toggle-label]');
            if (toggleLabel) toggleLabel.textContent = isCollapsed ? 'Expand' : 'Collapse';
        }
        try {
            localStorage.setItem(sidebarStorageKey, isCollapsed ? '1' : '0');
        } catch (error) {
            // Ignore storage errors in private browsing contexts.
        }
    };

    if (sidebarToggle && adminSidebar) {
        let savedCollapsed = false;
        try {
            savedCollapsed = localStorage.getItem(sidebarStorageKey) === '1';
        } catch (error) {
            savedCollapsed = false;
        }
        setSidebarCollapsed(savedCollapsed);

        sidebarToggle.addEventListener('click', () => {
            setSidebarCollapsed(!adminSidebar.classList.contains('collapsed'));
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setSidebarOpen(false);
        }
    });

    document.addEventListener('click', (event) => {
        const navLink = event.target.closest('[data-sidebar-link]');
        if (navLink && mobileSidebar && mobileSidebar.contains(navLink)) {
            setSidebarOpen(false);
        }
    });

    // Sidebar Collapsible Groups
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-sidebar-group-trigger]');
        if (trigger) {
            const menu = trigger.nextElementSibling;
            if (menu) {
                const isExpanded = trigger.getAttribute('aria-expanded') === 'true';
                trigger.setAttribute('aria-expanded', !isExpanded);
                menu.classList.toggle('grid-rows-[0fr]', isExpanded);
                menu.classList.toggle('grid-rows-[1fr]', !isExpanded);
                const icon = trigger.querySelector('[data-sidebar-group-icon]');
                if (icon) icon.classList.toggle('rotate-90');
            }
        }
    });

    // 2. Select All Table Checkboxes
    document.addEventListener('change', (event) => {
        const selectAll = event.target.closest('[data-select-all]');
        if (selectAll) {
            const table = selectAll.closest('table');
            if (table) {
                const checkboxes = table.querySelectorAll('tbody input[type="checkbox"]');
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = selectAll.checked;
                });
            }
        }
    });

    // 3. User Menu Dropdown in Topbar
    const userMenuTrigger = document.querySelector('[data-user-menu-trigger]');
    const userMenu = document.querySelector('[data-user-menu]');
    if (userMenuTrigger && userMenu) {
        userMenuTrigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const isExpanded = userMenuTrigger.getAttribute('aria-expanded') === 'true';
            userMenuTrigger.setAttribute('aria-expanded', !isExpanded);
            userMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', (event) => {
            if (!userMenu.contains(event.target) && !userMenuTrigger.contains(event.target)) {
                userMenu.classList.add('hidden');
                userMenuTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // 4. File/Image Preview Handler
    document.addEventListener('change', (event) => {
        const input = event.target.closest('[data-image-preview-input]');
        if (input && input.files && input.files[0]) {
            const container = input.closest('[data-image-preview-container]');
            if (container) {
                const preview = container.querySelector('[data-image-preview]');
                if (preview) {
                    const reader = new FileReader();
                    reader.onload = (readerEvent) => {
                        preview.src = readerEvent.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        }
    });

    // 5. Toast / Flash Dismissals
    document.addEventListener('click', (event) => {
        const dismissBtn = event.target.closest('[data-toast-dismiss]');
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

    // 6. Category Management Drawer
    const categoryPage = document.querySelector('[data-category-page]');
    if (categoryPage) {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const hasFinePointer = window.matchMedia('(pointer: fine)').matches;
        if (!prefersReducedMotion && hasFinePointer) {
            const cursorGlow = document.createElement('span');
            cursorGlow.className = 'category-cursor-glow';
            cursorGlow.setAttribute('aria-hidden', 'true');
            document.body.append(cursorGlow);

            let glowX = 0;
            let glowY = 0;
            let targetX = 0;
            let targetY = 0;
            let isAnimating = false;

            const animateGlow = () => {
                glowX += (targetX - glowX) * 0.18;
                glowY += (targetY - glowY) * 0.18;
                cursorGlow.style.transform = `translate3d(${glowX - 22}px, ${glowY - 22}px, 0)`;

                if (Math.abs(targetX - glowX) > 0.2 || Math.abs(targetY - glowY) > 0.2) {
                    requestAnimationFrame(animateGlow);
                } else {
                    isAnimating = false;
                }
            };

            window.addEventListener('pointermove', (event) => {
                targetX = event.clientX;
                targetY = event.clientY;
                cursorGlow.classList.add('is-visible');
                if (!isAnimating) {
                    isAnimating = true;
                    glowX = glowX || targetX;
                    glowY = glowY || targetY;
                    requestAnimationFrame(animateGlow);
                }
            }, { passive: true });

            window.addEventListener('pointerdown', () => cursorGlow.classList.add('is-pressed'), { passive: true });
            window.addEventListener('pointerup', () => cursorGlow.classList.remove('is-pressed'), { passive: true });
            document.addEventListener('mouseleave', () => cursorGlow.classList.remove('is-visible'));
        }

        const drawer = categoryPage.querySelector('[data-category-drawer]');
        const overlay = categoryPage.querySelector('[data-category-drawer-overlay]');
        const closeBtn = categoryPage.querySelector('[data-category-drawer-close]');
        const saveBtn = categoryPage.querySelector('[data-category-save]');
        const saveLabel = categoryPage.querySelector('[data-category-save-label]');
        const viewEditBtn = categoryPage.querySelector('[data-category-view-edit]');
        const formActions = categoryPage.querySelector('[data-category-form-actions]');
        const form = categoryPage.querySelector('[data-category-form]');
        const viewPanel = categoryPage.querySelector('[data-category-view-panel]');
        const title = categoryPage.querySelector('#category-drawer-title');
        const imagePathInput = categoryPage.querySelector('[data-category-image-path]');
        const nameInput = categoryPage.querySelector('[data-category-name-input]');
        const descriptionInput = categoryPage.querySelector('[data-category-description-input]');
        const statusInput = categoryPage.querySelector('[data-category-status-input]');
        const sortInput = categoryPage.querySelector('[data-category-sort-input]');
        const nameError = categoryPage.querySelector('[data-category-error-for="name"]');
        const searchInput = categoryPage.querySelector('[data-category-search]');
        const filterInput = categoryPage.querySelector('[data-category-filter]');
        const cards = Array.from(categoryPage.querySelectorAll('[data-category-card]'));
        const emptyAll = categoryPage.querySelector('[data-category-empty-all]');
        const emptyFilter = categoryPage.querySelector('[data-category-empty-filter]');
        const resetBtns = categoryPage.querySelectorAll('[data-category-reset]');
        const baseUrl = categoryPage.dataset.baseUrl || '';
        let lastFocused = null;
        let activeCategory = null;
        let mode = 'add';

        document.body.append(overlay, drawer);

        const setOverlayOpen = (isOpen) => {
            overlay.classList.toggle('hidden', !isOpen);
            requestAnimationFrame(() => {
                overlay.classList.toggle('opacity-0', !isOpen);
                overlay.classList.toggle('opacity-100', isOpen);
            });
            drawer.classList.toggle('translate-x-full', !isOpen);
            drawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            document.body.classList.toggle('overflow-hidden', isOpen);
        };

        const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])';
        const trapFocus = (event) => {
            if (event.key !== 'Tab' || drawer.getAttribute('aria-hidden') === 'true') return;
            const focusables = Array.from(drawer.querySelectorAll(focusableSelector)).filter((node) => node.offsetParent !== null);
            if (focusables.length === 0) return;
            const first = focusables[0];
            const last = focusables[focusables.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        };

        const setStatusBadge = (element, status) => {
            const isActive = status === 'active';
            element.className = `inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-bold transition duration-200 ${isActive ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-100 text-slate-500'}`;
            element.innerHTML = `<span class="h-2 w-2 rounded-full ${isActive ? 'bg-green-500' : 'bg-slate-400'}"></span>${isActive ? 'Active' : 'Inactive'}`;
        };

        const resolveImageUrl = (path) => {
            if (!path) return `${baseUrl}/public/assets/images/categories/category-fallback.svg`;
            if (path.startsWith('http://') || path.startsWith('https://')) return path;
            return `${baseUrl}/${path.replace(/^\//, '')}`;
        };

        const clearValidation = () => {
            nameInput.classList.remove('border-danger', 'focus:border-danger', 'focus:ring-danger');
            nameError.classList.add('hidden');
        };

        const resetForm = () => {
            form.reset();
            form.action = `${baseUrl}/admin/categories`;
            imagePathInput.value = '';
            if (statusInput.tagName === 'SELECT') {
                statusInput.value = 'active';
            } else {
                const hidden = statusInput.querySelector('[data-custom-select-value]');
                const text = statusInput.querySelector('[data-custom-select-text]');
                const opts = statusInput.querySelectorAll('[data-value]');
                if (hidden && text) {
                    hidden.value = 'active';
                    opts.forEach((opt) => {
                        opt.setAttribute('aria-selected', opt.dataset.value === 'active' ? 'true' : 'false');
                        if (opt.dataset.value === 'active') text.textContent = opt.textContent;
                    });
                }
            }
            sortInput.value = '0';
            clearValidation();
            saveBtn.disabled = false;
            if (saveLabel) saveLabel.textContent = 'Save';
            const preview = form.querySelector('[data-category-image-preview]');
            if (preview) {
                preview.src = resolveImageUrl('');
                preview.alt = 'Category image';
            }
        };

        const fillForm = (category) => {
            form.action = `${baseUrl}/admin/categories/${category.id}`;
            nameInput.value = category.name || '';
            descriptionInput.value = category.description || '';
            const statusVal = category.status || 'active';
            if (statusInput.tagName === 'SELECT') {
                statusInput.value = statusVal;
            } else {
                const hidden = statusInput.querySelector('[data-custom-select-value]');
                const text = statusInput.querySelector('[data-custom-select-text]');
                const opts = statusInput.querySelectorAll('[data-value]');
                if (hidden && text) {
                    hidden.value = statusVal;
                    opts.forEach((opt) => {
                        opt.setAttribute('aria-selected', opt.dataset.value === statusVal ? 'true' : 'false');
                        if (opt.dataset.value === statusVal) text.textContent = opt.textContent;
                    });
                }
            }
            sortInput.value = category.sort_order || 0;
            imagePathInput.value = category.image_path || '';
            const preview = form.querySelector('[data-category-image-preview]');
            if (preview) {
                preview.src = resolveImageUrl(category.image_path);
                preview.alt = category.name || 'Category image';
            }
            clearValidation();
        };

        const setMode = (nextMode, category = null) => {
            mode = nextMode;
            activeCategory = category;
            viewPanel.classList.toggle('hidden', mode !== 'view');
            form.classList.toggle('hidden', mode === 'view');
            formActions.classList.toggle('hidden', mode === 'view');
            formActions.classList.toggle('flex', mode !== 'view');
            viewEditBtn.classList.toggle('hidden', mode !== 'view');
            viewEditBtn.classList.toggle('inline-flex', mode === 'view');

            if (mode === 'add') {
                title.textContent = 'Add Category';
                resetForm();
                if (saveLabel) saveLabel.textContent = 'Save';
            } else if (mode === 'edit' && category) {
                title.textContent = 'Edit Category';
                fillForm(category);
                if (saveLabel) saveLabel.textContent = 'Save';
            } else if (mode === 'view' && category) {
                title.textContent = 'Category Details';
                const dates = window.GiftvibeCategoryDates?.[category.id] || {};
                const viewImage = viewPanel.querySelector('[data-category-view-image]');
                if (viewImage) {
                    viewImage.src = resolveImageUrl(category.image_path);
                    viewImage.alt = category.name || 'Category image';
                }
                viewPanel.querySelector('[data-category-view-name]').textContent = category.name || '';
                viewPanel.querySelector('[data-category-view-products]').textContent = `${category.products_count || 0} products`;
                viewPanel.querySelector('[data-category-view-description]').textContent = category.description || 'GiftVibe collection.';
                viewPanel.querySelector('[data-category-view-slug]').textContent = category.slug || '-';
                viewPanel.querySelector('[data-category-view-created]').textContent = dates.created || 'Not available';
                viewPanel.querySelector('[data-category-view-updated]').textContent = dates.updated || 'Not available';
                setStatusBadge(viewPanel.querySelector('[data-category-view-status]'), category.status || 'active');
            }
        };

        const openDrawer = (nextMode, category = null, opener = null) => {
            lastFocused = opener || document.activeElement;
            setMode(nextMode, category);
            setOverlayOpen(true);
            setTimeout(() => (nextMode === 'view' ? closeBtn : nameInput).focus(), 220);
        };

        const closeDrawer = () => {
            setOverlayOpen(false);
            if (lastFocused && typeof lastFocused.focus === 'function') {
                setTimeout(() => lastFocused.focus(), 220);
            }
        };

        const getFilterValue = () => {
            if (filterInput.tagName === 'SELECT') return filterInput.value || 'all';
            const hidden = filterInput.querySelector('[data-custom-select-value]');
            return hidden ? (hidden.value || 'all') : 'all';
        };

        const setFilterValue = (val) => {
            if (filterInput.tagName === 'SELECT') {
                filterInput.value = val;
                return;
            }
            const hidden = filterInput.querySelector('[data-custom-select-value]');
            const text = filterInput.querySelector('[data-custom-select-text]');
            const opts = filterInput.querySelectorAll('[data-value]');
            if (hidden && text) {
                hidden.value = val;
                opts.forEach((opt) => {
                    if (opt.dataset.value === val) {
                        opt.setAttribute('aria-selected', 'true');
                        text.textContent = opt.textContent;
                    } else {
                        opt.setAttribute('aria-selected', 'false');
                    }
                });
            }
        };

        const filterCards = () => {
            const query = (searchInput.value || '').trim().toLowerCase();
            const status = getFilterValue();
            let visible = 0;
            cards.forEach((card) => {
                const matchesSearch = !query || (card.dataset.categoryName || '').includes(query);
                const matchesStatus = status === 'all' || card.dataset.categoryStatus === status;
                const show = matchesSearch && matchesStatus;
                card.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            emptyAll.classList.toggle('hidden', cards.length > 0);
            emptyFilter.classList.toggle('hidden', cards.length === 0 || visible > 0);
        };

        categoryPage.addEventListener('click', (event) => {
            const addBtn = event.target.closest('[data-category-add]');
            const viewBtn = event.target.closest('[data-category-view]');
            const editBtn = event.target.closest('[data-category-edit]');
            const deleteBtn = event.target.closest('[data-category-view-delete]');

            if (addBtn) openDrawer('add', null, addBtn);
            if (viewBtn) {
                const card = viewBtn.closest('[data-category-card]');
                openDrawer('view', JSON.parse(card.dataset.category || '{}'), viewBtn);
            }
            if (editBtn) {
                const card = editBtn.closest('[data-category-card]');
                openDrawer('edit', JSON.parse(card.dataset.category || '{}'), editBtn);
            }
            if (deleteBtn && activeCategory) {
                if (confirm('Are you sure you want to delete "' + (activeCategory.name || 'this category') + '"? This action cannot be undone.')) {
                    const deleteForm = document.createElement('form');
                    deleteForm.method = 'POST';
                    deleteForm.action = `${baseUrl}/admin/categories/${activeCategory.id}/delete`;
                    const token = document.querySelector('input[name="_token"]')?.value || '';
                    deleteForm.innerHTML = '<input name="_token" value="' + token + '" type="hidden">';
                    document.body.appendChild(deleteForm);
                    deleteForm.submit();
                }
            }
        });

        viewEditBtn.addEventListener('click', () => {
            if (activeCategory) setMode('edit', activeCategory);
        });
        closeBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);

        // Image preview on file select
        const imageInput = form.querySelector('[data-category-image-input]');
        if (imageInput) {
            imageInput.addEventListener('change', () => {
                const file = imageInput.files?.[0];
                const preview = form.querySelector('[data-category-image-preview]');
                if (file && preview) {
                    const reader = new FileReader();
                    reader.onload = (e) => { preview.src = e.target?.result || preview.src; };
                    reader.readAsDataURL(file);
                }
            });
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') closeDrawer();
            trapFocus(event);
        });

        searchInput.addEventListener('input', filterCards);
        if (filterInput.tagName === 'SELECT') {
            filterInput.addEventListener('change', filterCards);
        } else {
            filterInput.addEventListener('change', filterCards);
        }
        resetBtns.forEach((resetBtn) => {
            resetBtn.addEventListener('click', () => {
                searchInput.value = '';
                setFilterValue('all');
                filterCards();
                searchInput.focus();
            });
        });

        saveBtn.addEventListener('click', () => {
            clearValidation();
            if (!nameInput.value.trim()) {
                nameInput.classList.add('border-danger', 'focus:border-danger', 'focus:ring-danger');
                nameError.classList.remove('hidden');
                nameInput.focus();
                return;
            }
            saveBtn.disabled = true;
            if (saveLabel) saveLabel.textContent = 'Saving...';
            form.submit();
        });

        filterCards();
    }

    // 7. Product Drawer (Add product)
    const productsPage = document.querySelector('[data-products-page]');
    if (productsPage) {
        const drawer = productsPage.querySelector('[data-product-drawer]');
        const overlay = productsPage.querySelector('[data-product-drawer-overlay]');
        const closeBtns = productsPage.querySelectorAll('[data-product-drawer-close]');
        const addBtn = productsPage.querySelector('[data-product-add]');
        const saveBtn = productsPage.querySelector('[data-product-save]');
        const saveLabel = productsPage.querySelector('[data-product-save-label]');
        const form = productsPage.querySelector('[data-product-form]');
        const title = productsPage.querySelector('[data-product-drawer-title]');
        const nameInput = productsPage.querySelector('[data-product-name-input]');
        const unitInput = productsPage.querySelector('[data-product-unit-input]');
        const basePriceInput = productsPage.querySelector('[data-product-base-price-input]');
        const salePriceInput = productsPage.querySelector('[data-product-sale-price-input]');
        const stockInput = productsPage.querySelector('[data-product-stock-input]');
        const mainImageInput = productsPage.querySelector('[data-product-main-image]');
        const mainImageLabel = productsPage.querySelector('[data-product-main-file-label]');
        const shortDescriptionInput = productsPage.querySelector('[data-product-short-description-input]');
        const descriptionInput = productsPage.querySelector('[data-product-description-input]');
        const featuredInput = productsPage.querySelector('[data-product-featured-input]');
        const statusInput = productsPage.querySelector('[data-product-status-input]');
        const categoryDropdown = productsPage.querySelector('[data-product-category-dropdown]');
        const categorySummary = productsPage.querySelector('[data-product-category-summary]');
        const categoryOptions = productsPage.querySelectorAll('[data-product-category-option]');

        let lastFocused = null;
        const baseUrl = productsPage.dataset.baseUrl || '';

        const setOverlayOpen = (isOpen) => {
            overlay.classList.toggle('hidden', !isOpen);
            requestAnimationFrame(() => {
                overlay.classList.toggle('opacity-0', !isOpen);
                overlay.classList.toggle('opacity-100', isOpen);
            });
            drawer.classList.toggle('translate-x-full', !isOpen);
            drawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            document.body.classList.toggle('overflow-hidden', isOpen);
        };

        const resetDrawer = () => {
            form.reset();
            form.action = `${baseUrl}/admin/products`;
            if (title) title.textContent = 'Add Product';
            if (saveLabel) saveLabel.textContent = 'Save';
            if (mainImageInput) mainImageInput.required = true;
            if (mainImageLabel) mainImageLabel.textContent = 'Upload product image';
            document.getElementById('variants-container')?.replaceChildren();
            updateCategorySummary();
        };

        const fillDrawer = (product) => {
            form.reset();
            form.action = `${baseUrl}/admin/products/${product.id}`;
            if (title) title.textContent = 'Edit Product';
            if (saveLabel) saveLabel.textContent = 'Save';
            if (nameInput) nameInput.value = product.name || '';
            if (unitInput) unitInput.value = product.unit || '';
            if (basePriceInput) basePriceInput.value = product.base_price || '';
            if (salePriceInput) salePriceInput.value = product.sale_price || '';
            if (stockInput) stockInput.value = product.stock_quantity ?? 0;
            if (shortDescriptionInput) shortDescriptionInput.value = product.short_description || '';
            if (descriptionInput) descriptionInput.value = product.description || '';
            if (featuredInput) featuredInput.checked = Number(product.is_featured || 0) === 1;
            if (statusInput) statusInput.value = product.status || 'active';
            if (mainImageInput) mainImageInput.required = false;
            if (mainImageLabel) mainImageLabel.textContent = 'Upload new product image';
            const selectedCategories = new Set((product.category_ids || []).map(String));
            categoryOptions.forEach((option) => {
                option.checked = selectedCategories.has(option.value);
            });
            document.getElementById('variants-container')?.replaceChildren();
            updateCategorySummary();
        };

        const openDrawer = (opener, product = null) => {
            lastFocused = opener || document.activeElement;
            if (product) {
                fillDrawer(product);
            } else {
                resetDrawer();
            }
            setOverlayOpen(true);
            setTimeout(() => {
                const firstInput = form.querySelector('input, select, textarea');
                if (firstInput) firstInput.focus();
            }, 220);
        };

        const closeDrawer = () => {
            setOverlayOpen(false);
            if (lastFocused && typeof lastFocused.focus === 'function') {
                setTimeout(() => lastFocused.focus(), 220);
            }
        };

        if (addBtn) addBtn.addEventListener('click', (e) => openDrawer(e.currentTarget));
        productsPage.querySelectorAll('[data-product-edit]').forEach((editBtn) => {
            editBtn.addEventListener('click', (event) => {
                const card = event.currentTarget.closest('[data-product-card]');
                const product = JSON.parse(card?.dataset.product || '{}');
                openDrawer(event.currentTarget, product);
            });
        });
        closeBtns.forEach((btn) => btn.addEventListener('click', closeDrawer));
        if (overlay) overlay.addEventListener('click', closeDrawer);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && drawer.getAttribute('aria-hidden') === 'false') closeDrawer();
        });

        if (saveBtn && form) {
            saveBtn.addEventListener('click', () => {
                saveBtn.disabled = true;
                if (saveLabel) saveLabel.textContent = 'Saving...';
                form.submit();
            });
        }

        const updateCategorySummary = () => {
            if (!categorySummary) return;
            const selected = Array.from(categoryOptions).filter((option) => option.checked);
            if (selected.length === 0) {
                categorySummary.textContent = 'Select categories';
            } else if (selected.length === 1) {
                categorySummary.textContent = selected[0].closest('label')?.textContent.trim() || '1 category selected';
            } else {
                categorySummary.textContent = `${selected.length} categories selected`;
            }
        };

        categoryOptions.forEach((option) => {
            option.addEventListener('change', updateCategorySummary);
        });

        document.addEventListener('click', (event) => {
            if (categoryDropdown && !categoryDropdown.contains(event.target)) {
                categoryDropdown.removeAttribute('open');
            }
        });

        updateCategorySummary();
    }

    // ── Custom Select Initialization ──
    document.querySelectorAll('[data-custom-select]').forEach((container) => {
        const trigger = container.querySelector('[data-custom-select-trigger]');
        const options = container.querySelector('[data-custom-select-options]');
        const hiddenInput = container.querySelector('[data-custom-select-value]');
        const textSpan = container.querySelector('[data-custom-select-text]');
        const optionItems = container.querySelectorAll('[data-value]');

        if (!trigger || !options || !hiddenInput) return;

        const close = () => {
            container.setAttribute('aria-expanded', 'false');
        };

        const open = () => {
            document.querySelectorAll('[data-custom-select][aria-expanded="true"]').forEach((el) => {
                if (el !== container) el.setAttribute('aria-expanded', 'false');
            });
            container.setAttribute('aria-expanded', 'true');
            options.scrollTop = 0;
        };

        const toggle = () => {
            if (container.getAttribute('aria-expanded') === 'true') {
                close();
            } else {
                open();
            }
        };

        const select = (value, label) => {
            hiddenInput.value = value;
            textSpan.textContent = label;
            optionItems.forEach((opt) => {
                opt.setAttribute('aria-selected', opt.dataset.value === value ? 'true' : 'false');
            });
            container.dispatchEvent(new CustomEvent('change', { detail: { value, label } }));
            close();
            trigger.focus();
        };

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggle();
        });

        optionItems.forEach((item) => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                select(item.dataset.value, item.textContent);
            });
        });

        // Keyboard navigation
        container.addEventListener('keydown', (e) => {
            const items = [...options.querySelectorAll('[data-value]')];
            const currentIndex = items.findIndex((el) => el.getAttribute('aria-selected') === 'true');

            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (container.getAttribute('aria-expanded') === 'true') {
                    const selected = options.querySelector('[aria-selected="true"]');
                    if (selected) select(selected.dataset.value, selected.textContent);
                } else {
                    open();
                }
            } else if (e.key === 'Escape') {
                close();
                trigger.focus();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (container.getAttribute('aria-expanded') !== 'true') {
                    open();
                } else {
                    const next = items[Math.min(currentIndex + 1, items.length - 1)];
                    if (next) {
                        next.setAttribute('aria-selected', 'true');
                        items.forEach((el, i) => { if (i !== Math.min(currentIndex + 1, items.length - 1)) el.setAttribute('aria-selected', 'false'); });
                        next.scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (container.getAttribute('aria-expanded') !== 'true') {
                    open();
                } else {
                    const prev = items[Math.max(currentIndex - 1, 0)];
                    if (prev) {
                        prev.setAttribute('aria-selected', 'true');
                        items.forEach((el, i) => { if (i !== Math.max(currentIndex - 1, 0)) el.setAttribute('aria-selected', 'false'); });
                        prev.scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'Home') {
                e.preventDefault();
                if (items.length > 0) {
                    items[0].setAttribute('aria-selected', 'true');
                    items.forEach((el, i) => { if (i !== 0) el.setAttribute('aria-selected', 'false'); });
                    items[0].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'End') {
                e.preventDefault();
                const last = items.length - 1;
                items[last].setAttribute('aria-selected', 'true');
                items.forEach((el, i) => { if (i !== last) el.setAttribute('aria-selected', 'false'); });
                items[last].scrollIntoView({ block: 'nearest' });
            }
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) close();
        });

        // Close on scroll outside
        window.addEventListener('scroll', close, { passive: true });
    });
});
