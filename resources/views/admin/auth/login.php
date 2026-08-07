<?php
declare(strict_types=1);

$error = $error ?? null;
$csrfToken = $csrfToken ?? '';
$logo = $logo ?? '/assets/images/logo.svg';
$siteName = $siteName ?? 'GiftVibe';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-primary/5">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign In | <?= htmlspecialchars($siteName) ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <?php require BASE_PATH . '/resources/views/components/admin/tailwind-head.php'; ?>
</head>
<body class="flex min-h-full flex-col justify-center bg-primary/5 px-4 py-12 font-sans text-secondary antialiased sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Main Card -->
        <div class="relative overflow-hidden rounded-3xl border border-primary/10 bg-white px-8 py-10 shadow-xl sm:px-10">
            <!-- Accent glow border top -->
            <div class="absolute top-0 inset-x-0 h-1.5 bg-primary"></div>

            <!-- Card Logo (Top) -->
            <div class="flex justify-center mb-6">
                <div class="relative rounded-2xl border border-primary/10 bg-primary/5 p-1 shadow-inner">
                    <img src="<?= htmlspecialchars($logo) ?>" 
                         alt="<?= htmlspecialchars($siteName) ?> Logo" 
                         class="h-16 w-16 rounded-xl object-contain" 
                         onerror="this.src='/assets/images/logo.svg'">
                </div>
            </div>

            <!-- Login Text -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black tracking-tight text-secondary">Admin Sign In</h2>
                <p class="mt-1.5 text-sm text-secondary/65">Sign in to <?= htmlspecialchars($siteName) ?> dashboard</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-5 flex items-start gap-2.5 rounded-2xl border border-accent/20 bg-accent/10 p-4 text-sm font-semibold text-accent">
                    <iconify-icon icon="heroicons:exclamation-circle-solid" width="18" height="18" class="mt-0.5 shrink-0 text-accent"></iconify-icon>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form class="space-y-5" action="/admin/login" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div>
                    <label for="phone" class="mb-2 block text-xs font-bold uppercase tracking-wider text-secondary/65">Registered Phone Number</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-secondary/50">
                            <iconify-icon icon="heroicons:phone-solid" width="18" height="18"></iconify-icon>
                        </span>
                        <input id="phone" name="phone" type="tel" required 
                               class="w-full rounded-2xl border border-primary/10 bg-primary/5 py-3.5 pl-11 pr-4 text-sm text-secondary outline-none transition placeholder:text-secondary/40 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10" 
                               placeholder="e.g. 0768306759">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="flex w-full cursor-pointer justify-center rounded-2xl border border-transparent bg-primary px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary/20 transition duration-150 hover:bg-secondary focus:outline-none focus:ring-4 focus:ring-primary/20">
                        Get OTP Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
