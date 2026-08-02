<?php

declare(strict_types=1);

/**
 * Global footer — shared by the public and admin layouts.
 */
?>
<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 py-8 sm:flex-row sm:px-6 lg:px-8">
        <p class="text-sm text-slate-500">
            &copy; <?php require BASE_PATH . '/resources/views/components/shared/date.php'; ?>
            <span class="font-semibold text-secondary">Gift<span class="text-primary">Vibe</span></span>
            . All rights reserved.
        </p>
        <p class="text-xs text-slate-400">Core PHP MVC &middot; Tailwind CSS v4</p>
    </div>
</footer>
