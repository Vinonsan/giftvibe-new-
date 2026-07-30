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
<html lang="en" class="h-full bg-light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Dashboard') ?> | Gift Vibe LK</title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('/public/assets/icons/giftvibe-mark.svg')) ?>">
    <link rel="apple-touch-icon" href="<?= e(asset('/public/assets/icons/giftvibe-mark.svg')) ?>">
    <style><?= themeCssVariables() ?></style>
    
    <!-- Compiled Tailwind CSS or Fallback CDN -->
    <?php if (file_exists(ADMIN_PATH . '/assets/css/style.css')): ?>
        <link rel="stylesheet" href="<?= asset('/admin/assets/css/style.css') ?>">
    <?php elseif (file_exists(PUBLIC_PATH . '/assets/css/style.css')): ?>
        <link rel="stylesheet" href="<?= asset('/public/assets/css/style.css') ?>">
    <?php elseif (file_exists(PUBLIC_PATH . '/assets/css/app.min.css')): ?>
        <link rel="stylesheet" href="<?= asset('/public/assets/css/app.min.css') ?>">
    <?php else: ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: <?= adminTailwindColorsJs() ?>,
                        borderRadius: {
                            button: '0.625rem',
                            card: '0.75rem',
                        },
                        boxShadow: {
                            card: '0 8px 20px -16px rgba(12, 43, 78, 0.45)',
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
<body class="min-h-full overflow-x-hidden bg-light font-sans text-slate-800 antialiased">

    <!-- Mobile Sidebar overlay / drawer -->
    <?php component('admin/components/navigation/mobile-sidebar', ['navigation' => $adminNavigation ?? []]); ?>

    <div class="min-h-screen max-w-full lg:flex" data-admin-shell>
        <!-- Desktop Sidebar -->
        <?php component('admin/components/navigation/sidebar', ['navigation' => $adminNavigation ?? []]); ?>

        <!-- Main Content Area -->
        <div class="flex min-w-0 flex-1 flex-col" data-admin-main>
            <!-- Topbar -->
            <?php component('admin/components/navigation/topbar', [
                'title' => $title ?? 'Dashboard',
                'subtitle' => $subtitle ?? null,
            ]); ?>

            <!-- Content Panel -->
            <main class="min-w-0 flex-1 overflow-x-hidden bg-light px-3 py-3 focus:outline-none sm:px-6 sm:py-6 lg:px-8">
                <div class="mx-auto w-full max-w-none space-y-5">
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
    </div>

    <!-- Admin JS -->
    <script src="<?= asset('/admin/assets/js/admin.js') ?>"></script>
</body>
</html>
