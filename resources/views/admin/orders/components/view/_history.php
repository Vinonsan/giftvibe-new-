<?php
declare(strict_types=1);
$statusLabel = static function (string $status): string {
    return ucfirst(str_replace('_', ' ', $status));
};
?>
<?php if ($statusHistory): ?>
    <div class="relative space-y-0">
        <?php foreach ($statusHistory as $index => $entry): ?>
            <div class="relative flex gap-4 pb-8 <?= $index === count($statusHistory) - 1 ? 'pb-0' : '' ?>">
                <?php if ($index !== count($statusHistory) - 1): ?>
                    <span class="absolute left-[11px] top-6 h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                <?php endif; ?>
                <span class="relative z-10 mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/10 ring-4 ring-white">
                    <span class="h-2 w-2 rounded-full bg-primary"></span>
                </span>
                <div class="min-w-0 flex-1 rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-bold text-secondary"><?= htmlspecialchars($statusLabel((string) $entry['status'])) ?></p>
                        <time class="text-xs text-slate-400"><?= htmlspecialchars((string) ($entry['created_at'] ?? '')) ?></time>
                    </div>
                    <?php if (trim((string) ($entry['note'] ?? '')) !== ''): ?>
                        <p class="mt-1 text-sm text-slate-600"><?= htmlspecialchars((string) $entry['note']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($entry['changed_by_name'])): ?>
                        <p class="mt-1 text-xs text-slate-400">By <?= htmlspecialchars((string) $entry['changed_by_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm italic text-slate-400">No status history recorded yet.</p>
<?php endif; ?>
