<?php

declare(strict_types=1);

/**
 * Public layout — wraps every public page.
 * Common assets (fonts, Tailwind, global.css) are imported here once,
 * and the navbar/footer global components are included here.
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
    <title><?= htmlspecialchars($title) ?> | Gift Vibe</title>

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
<body class="flex min-h-screen flex-col bg-white font-sans text-slate-800 antialiased">
    <?php require BASE_PATH . '/resources/views/components/navigation/navbar.php'; ?>

    <main class="flex-1">
        <?= $content ?>
    </main>

    <?php require BASE_PATH . '/resources/views/components/navigation/footer.php'; ?>
</body>
</html>
