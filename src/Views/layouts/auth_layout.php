<?php
// src/Views/layouts/auth_layout.php
function renderAuthLayout(string $title, string $contentView, array $data = []): void
{
    extract($data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/img/giftvibe-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: <?= themeTailwindColorsJs() ?> } } };</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white text-dark antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <img src="<?= BASE_URL ?>/assets/img/giftvibe-logo.png" alt="<?= htmlspecialchars(APP_NAME) ?>" class="mx-auto mb-3 h-16 w-16 rounded-2xl object-cover">
                <h1 class="text-2xl font-bold text-primary-700"><?= APP_NAME ?></h1>
                <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($title) ?></p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
                <?php require_once $contentView; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}
