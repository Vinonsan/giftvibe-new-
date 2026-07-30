<?php
/**
 * Variables:
 * @var string $title
 * @var array|null $meta
 * @var array|null $navigation
 * @var string $content
 * @var array|null $footerData
 * @var array|null $structuredData
 */
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Gift Vibe LK') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('/public/assets/icons/giftvibe-mark.svg')) ?>">
    <link rel="apple-touch-icon" href="<?= e(asset('/public/assets/icons/giftvibe-mark.svg')) ?>">
    <style><?= themeCssVariables() ?></style>
    
    <!-- Tailwind CSS -->
    <?php if (file_exists(__DIR__ . '/../../../../public/assets/css/app.min.css')): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/app.min.css">
    <?php else: ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: <?= themeTailwindColorsJs() ?>,
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
    
    <!-- SEO Meta Component -->
    <?php component('public/components/common/seo-meta', ['meta' => $meta ?? []]); ?>

    <!-- Structured Data Component -->
    <?php if (!empty($structuredData)): ?>
        <?php component('public/components/common/structured-data', ['data' => $structuredData]); ?>
    <?php endif; ?>
</head>
<body class="bg-slate-50 text-slate-900 font-sans flex flex-col min-h-screen">

    <!-- Skip Link Component -->
    <?php component('public/components/common/skip-link'); ?>

    <!-- Announcement Bar Component -->
    <?php component('public/components/common/announcement-bar'); ?>

    <!-- Header Component -->
    <?php component('public/components/common/header', ['navigation' => $navigation ?? []]); ?>

    <!-- Main Content Slot -->
    <main id="main-content" class="flex-grow focus:outline-none" tabindex="-1">
        <?= $content ?>
    </main>

    <!-- Footer Component -->
    <?php component('public/components/common/footer', ['footerData' => $footerData ?? []]); ?>

    <!-- Public JS -->
    <script src="<?= asset('/resources/js/public.js') ?>"></script>
</body>
</html>
