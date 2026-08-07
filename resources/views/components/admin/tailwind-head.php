<?php
declare(strict_types=1);

$globalCssPath = BASE_PATH . '/public/assets/css/global.css';
$globalCss = is_readable($globalCssPath) ? (string) file_get_contents($globalCssPath) : '';
$tailwindLocal = app_asset('js/tailwindcss-browser.js');
$tailwindCdn1 = 'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4';
$tailwindCdn2 = 'https://unpkg.com/@tailwindcss/browser@4';
$tailwindPrimary = is_readable(BASE_PATH . '/public/assets/js/tailwindcss-browser.js') ? $tailwindLocal : $tailwindCdn1;
$tailwindFallback = $tailwindPrimary === $tailwindLocal ? $tailwindCdn1 : $tailwindCdn2;
?>
<link rel="stylesheet" href="<?= app_asset('css/admin-critical.css') ?>">
<script src="<?= htmlspecialchars($tailwindPrimary) ?>" crossorigin="anonymous" onerror="this.onerror=null;var s=document.createElement('script');s.src=<?= json_encode($tailwindFallback, JSON_UNESCAPED_SLASHES) ?>;s.crossOrigin='anonymous';s.onerror=function(){var s2=document.createElement('script');s2.src=<?= json_encode($tailwindCdn2, JSON_UNESCAPED_SLASHES) ?>;s2.crossOrigin='anonymous';document.head.appendChild(s2);};document.head.appendChild(s);"></script>
<style type="text/tailwindcss"><?= $globalCss ?></style>
