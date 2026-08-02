<?php

declare(strict_types=1);

$title = $title ?? 'Home';
$content = $content ?? '';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$metaDescription = trim((string) ($metaDescription ?? 'Find thoughtful gifts for every person and celebration at GiftVibe.'));
$canonicalPath = '/' . ltrim((string) ($canonicalPath ?? $currentPath), '/');
$host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$siteUrl = rtrim((string) (getenv('APP_URL') ?: "{$scheme}://{$host}"), '/');
$canonicalUrl = $siteUrl . ($canonicalPath === '/' ? '/' : rtrim($canonicalPath, '/'));
$ogImage = (string) ($ogImage ?? '/assets/images/giftvibe-mark.svg');
$ogImageUrl = str_starts_with($ogImage, 'http') ? $ogImage : $siteUrl . '/' . ltrim($ogImage, '/');
$structuredData = $structuredData ?? null;
if (is_array($structuredData) && ($structuredData['url'] ?? '') === '/') {
    $structuredData['url'] = $siteUrl . '/';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Gift Vibe</title>
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="GiftVibe">
    <meta property="og:title" content="<?= htmlspecialchars($title) ?> | Gift Vibe">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImageUrl) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($title) ?> | Gift Vibe">
    <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImageUrl) ?>">
    <?php if (is_array($structuredData)): ?>
        <script type="application/ld+json"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        <?= file_get_contents(BASE_PATH . '/public/assets/css/global.css') ?>
    </style>
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-slate-800 antialiased">
    <?php require BASE_PATH . '/resources/views/components/navigation/navbar.php'; ?>

    <main class="flex-1 px-8 py-12 gap-6">
        <?= $content ?>
    </main>

    <?php require BASE_PATH . '/resources/views/components/navigation/public-footer.php'; ?>
</body>
</html>
