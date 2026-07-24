<?php
/**
 * Variables:
 * @var array|null $meta Configurable meta object e.g. ['description' => '...', 'image' => '...', 'url' => '...']
 */
$description = $meta['description'] ?? 'Gift Vibe LK - The best handpicked gift collection in Sri Lanka.';
$image = $meta['image'] ?? BASE_URL . '/public/assets/img/giftvibe-logo.png';
$url = $meta['url'] ?? (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '');
?>
<meta name="description" content="<?= e($description) ?>">
<!-- Open Graph -->
<meta property="og:title" content="<?= e($title ?? 'Gift Vibe LK') ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:image" content="<?= e($image) ?>">
<meta property="og:url" content="<?= e($url) ?>">
<meta property="og:type" content="website">
<link rel="canonical" href="<?= e($url) ?>">