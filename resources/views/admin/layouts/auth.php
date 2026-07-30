<?php
/**
 * Variables:
 * @var string $title
 * @var string $content
 * @var string|null $error
 */
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Sign In') ?> | Gift Vibe LK</title>
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
<body class="h-full font-sans text-slate-800 antialiased flex items-center justify-center p-4">

    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-card shadow-card border border-slate-200">
        <!-- Logo Header -->
        <div class="text-center">
            <?php component('public/components/common/logo', ['size' => 'lg']); ?>
            <h2 class="mt-6 text-3xl font-extrabold text-slate-900"><?= e($title) ?></h2>
        </div>

        <!-- Inline Error Alerts -->
        <?php if (!empty($error)): ?>
            <?php component('admin/components/feedback/alert', [
                'type' => 'danger',
                'message' => $error
            ]); ?>
        <?php endif; ?>

        <!-- Form content -->
        <?= $content ?>
    </div>

    <!-- Admin JS -->
    <script src="<?= asset('/admin/assets/js/admin.js') ?>"></script>
</body>
</html>
