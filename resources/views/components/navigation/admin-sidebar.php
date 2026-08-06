<?php

declare(strict_types=1);

use App\Core\Database;

$sidebarMenu = $sidebarMenu ?? [];
$currentPath = $currentPath ?? ($_SERVER['REQUEST_URI'] ?? '/admin');
$currentPath = rtrim((string) parse_url($currentPath, PHP_URL_PATH), '/') ?: '/';
$footerMenu = array_values(array_filter(
    $sidebarMenu,
    static fn (array $item): bool => ($item['placement'] ?? '') === 'footer'
));

$pdo = Database::connection();
$general = $pdo->query("SELECT * FROM general_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC) ?: [];
$logo = (string)($general['site_logo'] ?? '/assets/images/logo.svg');
$siteName = (string)($general['site_name'] ?? 'GiftVibe');
?>

<style>
[data-sidebar-nav]::-webkit-scrollbar {
    display: none;
}
[data-sidebar-nav] {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

<div id="sidebar-overlay"
     class="fixed inset-0 z-40 bg-secondary/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"
     data-sidebar-overlay></div>

<aside id="admin-sidebar"
       class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out lg:translate-x-0"
       data-sidebar>

    <button type="button"
            class="absolute top-[3.25rem] -right-3.5 z-50 hidden h-7 w-7 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 shadow-sm transition duration-200 hover:border-primary/20 hover:bg-primary/5 hover:text-primary cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/20 lg:flex"
            data-sidebar-collapse-toggle
            aria-label="Collapse sidebar">
        <svg class="h-4 w-4 transition-transform duration-300" data-collapse-toggle-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
    </button>

    <div class="shrink-0 border-b border-slate-100 p-3">
        <div class="flex h-14 items-center justify-between  px-3.5">
            <a href="/admin" class="flex min-w-0 items-center gap-3">
                <img src="<?= htmlspecialchars($logo) ?>"
                     alt="<?= htmlspecialchars($siteName) ?>"
                     class="h-9 w-9 shrink-0 rounded-lg shadow-sm object-contain border border-slate-100">
                <span class="truncate text-lg font-extrabold tracking-tight text-secondary" data-sidebar-logo-text>
                    <?= htmlspecialchars($siteName) ?>
                </span>
            </a>

            <button type="button"
                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 lg:hidden cursor-pointer"
                data-sidebar-close
                aria-label="Close sidebar">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            </button>
        </div>
    </div>

    <nav class="flex-1 space-y-3 overflow-y-auto px-3 py-3" data-sidebar-nav>
        <?php foreach ($sidebarMenu as $item):
            if (($item['placement'] ?? '') === 'footer') {
                continue;
            }


            $hasChildren = !empty($item['children']);
            $itemHref = rtrim((string) ($item['href'] ?? ''), '/') ?: '/';
            $isActive = ($item['active'] ?? false) || $itemHref === $currentPath;

            $childActive = false;
            if ($hasChildren) {
                foreach ($item['children'] as $child) {
                    $childHref = rtrim((string) ($child['href'] ?? ''), '/') ?: '/';
                    if (($child['active'] ?? false) || $childHref === $currentPath) {
                        $childActive = true;
                        $isActive = true;
                        break;
                    }
                }
            }
        ?>

        <?php if ($hasChildren): ?>
            <div data-sidebar-group>
                <button type="button"
                        class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition duration-200 cursor-pointer  
                               <?= $isActive ? 'bg-primary text-white shadow-sm shadow-primary/15' : 'text-slate-600 hover:bg-secondary/20 hover:text-secondary' ?>"
                        data-sidebar-toggle
                        aria-expanded="<?= $childActive ? 'true' : 'false' ?>">
                    <?php if (!empty($item['icon'])): ?>
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center <?= $isActive ? 'text-white' : 'text-slate-400' ?>"><?= $item['icon'] ?></span>
                    <?php endif; ?>
                    <span class="flex-1 text-left" data-sidebar-text><?= htmlspecialchars($item['label']) ?></span>
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-200 <?= $isActive ? 'text-white/70' : 'text-slate-400' ?> <?= $childActive ? 'rotate-90' : '' ?>"
                         data-sidebar-chevron data-sidebar-chevron-arrow viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div class="overflow-hidden transition-all duration-300 ease-in-out"
                     data-sidebar-submenu
                     style="<?= $childActive ? '' : 'max-height:0' ?>">
                    <div class="ml-5 mt-1 space-y-0.5 border-l border-slate-200 py-1 pl-4">
                        <?php foreach ($item['children'] as $child):
                            $childHref = rtrim((string) ($child['href'] ?? ''), '/') ?: '/';
                            $isChildActive = ($child['active'] ?? false) || $childHref === $currentPath;
                        ?>
                            <a href="<?= htmlspecialchars($child['href'] ?? '#') ?>"
                               class="block rounded-lg px-3 py-2 text-[13px] font-medium leading-5 transition duration-150
                                      <?= $isChildActive
                                          ? 'bg-secondary text-white font-semibold shadow-sm'
                                          : 'text-slate-500 hover:bg-slate-100 hover:text-secondary' ?>">
                                <?= htmlspecialchars($child['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= htmlspecialchars($item['href'] ?? '#') ?>"
               class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition duration-200
                      <?= $isActive ? 'bg-primary text-white shadow-sm shadow-primary/15' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                <?php if (!empty($item['icon'])): ?>
                    <span class="flex h-5 w-5 shrink-0 items-center justify-center <?= $isActive ? 'text-white' : 'text-slate-400' ?>"><?= $item['icon'] ?></span>
                <?php endif; ?>
                <span data-sidebar-text><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <?php if ($footerMenu): ?>
        <div class="shrink-0 border-t border-slate-100 bg-white p-3" data-sidebar-footer>
            <?php foreach ($footerMenu as $item):
                $itemHref = rtrim((string) ($item['href'] ?? ''), '/') ?: '/';
                $isActive = ($item['active'] ?? false) || $itemHref === $currentPath;
            ?>
                <a href="<?= htmlspecialchars((string) ($item['href'] ?? '#')) ?>"
                   class="flex min-h-11 items-center gap-3 rounded-xl border px-3 py-2.5 text-sm font-medium transition duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/20
                          <?= $isActive
                              ? 'border-secondary bg-secondary text-white shadow-sm'
                              : 'border-secondary/10 text-secondary hover:border-secondary/15 hover:bg-secondary/10' ?>">
                    <?php if (!empty($item['icon'])): ?>
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center <?= $isActive ? 'text-white' : 'text-secondary/60' ?>"><?= $item['icon'] ?></span>
                    <?php endif; ?>
                    <span data-sidebar-text><?= htmlspecialchars((string) ($item['label'] ?? '')) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</aside>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.sidebar) return;
    window.GiftVibeUI = window.GiftVibeUI || {};

    var sidebar = document.querySelector('[data-sidebar]');
    var overlay = document.querySelector('[data-sidebar-overlay]');
    var collapseToggle = document.querySelector('[data-sidebar-collapse-toggle]');
    var toggleIcon = document.querySelector('[data-collapse-toggle-icon]');
    if (!sidebar) return;

    function updateCollapseIcon(isCollapsed) {
        if (toggleIcon) {
            if (isCollapsed) {
                toggleIcon.classList.add('rotate-180');
            } else {
                toggleIcon.classList.remove('rotate-180');
            }
        }
    }

    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        updateCollapseIcon(true);
    }

    if (collapseToggle) {
        collapseToggle.addEventListener('click', function () {
            var isCollapsed = document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
            updateCollapseIcon(isCollapsed);

            if (isCollapsed) {
                sidebar.querySelectorAll('[data-sidebar-toggle]').forEach(function (btn) {
                    btn.setAttribute('aria-expanded', 'false');
                    var submenu = btn.closest('[data-sidebar-group]').querySelector('[data-sidebar-submenu]');
                    if (submenu) submenu.style.maxHeight = '0';
                    var chevron = btn.querySelector('[data-sidebar-chevron-arrow]');
                    if (chevron) chevron.classList.remove('rotate-90');
                });
            }
        });
    }

    sidebar.querySelectorAll('[data-sidebar-toggle]').forEach(function (btn) {
        var group = btn.closest('[data-sidebar-group]');
        var submenu = group.querySelector('[data-sidebar-submenu]');
        var chevron = btn.querySelector('[data-sidebar-chevron]');

        if (btn.getAttribute('aria-expanded') === 'true' && submenu) {
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
        }

        btn.addEventListener('click', function () {
            if (document.body.classList.contains('sidebar-collapsed')) {
                document.body.classList.remove('sidebar-collapsed');
                localStorage.setItem('sidebar-collapsed', 'false');
                updateCollapseIcon(false);
            }

            var expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');

            if (chevron) {
                chevron.classList.toggle('rotate-90', !expanded);
            }

            if (submenu) {
                if (expanded) {
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    requestAnimationFrame(function () { submenu.style.maxHeight = '0'; });
                } else {
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    var handler = function () {
                        submenu.style.maxHeight = 'none';
                        submenu.removeEventListener('transitionend', handler);
                    };
                    submenu.addEventListener('transitionend', handler);
                }
            }
        });
    });

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        if (overlay) {
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
        }
        document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
    }
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        if (overlay) {
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
        }
        document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('[data-sidebar-open]')) openSidebar();
        if (e.target.closest('[data-sidebar-close]')) closeSidebar();
        if (e.target.closest('[data-sidebar-overlay]')) closeSidebar();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

    window.GiftVibeUI.sidebar = true;
})();
</script>
