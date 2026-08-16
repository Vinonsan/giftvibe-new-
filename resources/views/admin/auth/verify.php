<?php
declare(strict_types=1);

$phone = $phone ?? '';
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
    <title>Verify OTP | <?= htmlspecialchars($siteName) ?></title>
    
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
                         onerror="this.src='<?= htmlspecialchars(app_asset('images/logo.svg'), ENT_QUOTES) ?>'">
                </div>
            </div>

            <!-- Login Text -->
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black tracking-tight text-secondary">Enter OTP Code</h2>
                <p class="mt-1.5 text-sm text-secondary/65">Security code sent to <span class="font-bold text-secondary"><?= htmlspecialchars($phone) ?></span></p>
            </div>

            <?php if ($error): ?>
                <div class="mb-5 flex items-start gap-2.5 rounded-2xl border border-accent/20 bg-accent/10 p-4 text-sm font-semibold text-accent">
                    <iconify-icon icon="heroicons:exclamation-circle-solid" width="18" height="18" class="mt-0.5 shrink-0 text-accent"></iconify-icon>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form class="space-y-6" action="<?= htmlspecialchars(app_url('/admin/login/verify')) ?>" method="POST" id="otp-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div>
                    <label class="mb-4 block text-center text-xs font-bold uppercase tracking-wider text-secondary/65">6-Digit Verification Code</label>
                    <div class="flex justify-between gap-2" id="otp-digits-container">
                        <?php for ($i = 0; $i < 6; $i++): ?>
                            <input type="text" name="otp_digit[]" maxlength="1" required
                                   class="h-14 w-11 rounded-2xl border border-primary/10 bg-primary/5 text-center text-xl font-extrabold text-secondary outline-none transition focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/10"
                                   pattern="[0-9]" inputmode="numeric" autocomplete="one-time-code">
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="flex w-full cursor-pointer justify-center rounded-2xl border border-transparent bg-primary px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary/20 transition duration-150 hover:bg-secondary focus:outline-none focus:ring-4 focus:ring-primary/20">
                        Verify & Login
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('otp-digits-container');
            const inputs = container.querySelectorAll('input');

            inputs.forEach((input, index) => {
                // Focus the first input initially
                if (index === 0) input.focus();

                input.addEventListener('input', (e) => {
                    const val = e.target.value;
                    if (val.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                // Support paste event for the entire OTP code
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const text = (e.clipboardData || window.clipboardData).getData('text').trim();
                    if (/^\d{6}$/.test(text)) {
                        inputs.forEach((inp, idx) => {
                            inp.value = text[idx];
                        });
                        inputs[5].focus();
                    }
                });
            });
        });
    </script>
</body>
</html>
