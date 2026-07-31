<?php

declare(strict_types=1);

/**
 * Admin layout — wraps every admin page.
 * Shares the same common assets (fonts, Tailwind, global.css) and
 * includes the admin sidebar, admin navbar and footer components.
 *
 * @var string $title
 * @var string $content
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Admin &mdash; Gift Vibe</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Global styles & brand colors — imported from global.css -->
    <style type="text/tailwindcss">
        <?= file_get_contents(BASE_PATH . '/public/assets/css/global.css') ?>
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased">
    <?php require BASE_PATH . '/resources/views/components/navigation/admin-sidebar.php'; ?>

    <div class="flex min-h-screen flex-col lg:pl-64">
        <?php require BASE_PATH . '/resources/views/components/navigation/admin-navbar.php'; ?>

        <main class="flex-1 p-6">
            <?= $content ?>
        </main>

        <?php require BASE_PATH . '/resources/views/components/navigation/footer.php'; ?>
    </div>
</body>
</html>
