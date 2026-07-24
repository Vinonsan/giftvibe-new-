<?php
/**
 * Variables:
 * @var string $title
 * @var string $content
 * @var array|null $adminNavigation
 * @var array|null $breadcrumbs
 * @var array|null $flashMessages
 */
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Dashboard') ?> | Gift Vibe LK</title>
    
    <!-- Compiled Tailwind CSS or Fallback CDN -->
    <?php if (file_exists(__DIR__ . '/../../../../public/assets/css/app.min.css')): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/app.min.css">
    <?php else: ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: <?= adminTailwindColorsJs() ?>,
                        borderRadius: {
                            button: '0.375rem',
                            card: '0.5rem',
                        },
                        boxShadow: {
                            card: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                        },
                        maxWidth: {
                            container: '1280px',
                        }
                    }
                }
            };
        </script>
    <?php endif; ?>
</head>
<body class="h-full font-sans text-slate-800 antialiased flex flex-col md:flex-row">

    <!-- Mobile Sidebar overlay / drawer -->
    <?php component('admin/components/navigation/mobile-sidebar', ['navigation' => $adminNavigation ?? []]); ?>

    <!-- Desktop Sidebar -->
    <?php component('admin/components/navigation/sidebar', ['navigation' => $adminNavigation ?? []]); ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Topbar -->
        <?php component('admin/components/navigation/topbar', ['title' => $title ?? 'Dashboard']); ?>

        <!-- Content Panel -->
        <main class="flex-1 relative overflow-y-auto focus:outline-none p-6">
            <div class="max-w-7xl mx-auto space-y-6">
                <!-- Breadcrumbs -->
                <?php if (!empty($breadcrumbs)): ?>
                    <?php component('admin/components/common/breadcrumbs', ['items' => $breadcrumbs]); ?>
                <?php endif; ?>

                <!-- Flash Messages -->
                <?php if (!empty($flashMessages)): ?>
                    <div class="space-y-2">
                        <?php foreach ($flashMessages as $type => $message): ?>
                            <?php component('admin/components/feedback/flash-message', ['type' => $type, 'message' => $message]); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Inner Slot -->
                <?= $content ?>
            </div>
        </main>
    </div>

    <!-- Admin JS -->
    <script src="<?= BASE_URL ?>/resources/js/admin.js"></script>
</body>
</html>
