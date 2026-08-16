<?php
declare(strict_types=1);
?>
<?php if ($notifications): ?>
    <div class="space-y-3">
        <?php foreach ($notifications as $note): ?>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <?php
                            $nStatus = (string) ($note['status'] ?? 'queued');
                            $nClass = match ($nStatus) {
                                'sent' => 'bg-emerald-50 text-emerald-700',
                                'failed' => 'bg-rose-50 text-rose-700',
                                default => 'bg-amber-50 text-amber-700',
                            };
                        ?>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold <?= $nClass ?>"><?= htmlspecialchars(ucfirst($nStatus)) ?></span>
                        <?php if (!empty($note['order_number'])): ?>
                            <a href="<?= htmlspecialchars(app_url('/admin/orders?view=' . (int) ($note['order_id'] ?? 0))) ?>" class="text-xs font-bold text-primary hover:underline"><?= htmlspecialchars((string) $note['order_number']) ?></a>
                        <?php endif; ?>
                    </div>
                    <time class="text-xs text-slate-400"><?= htmlspecialchars((string) ($note['created_at'] ?? '')) ?></time>
                </div>
                <p class="mt-2 text-sm text-secondary"><?= htmlspecialchars((string) $note['message']) ?></p>
                <p class="mt-1 text-xs text-slate-400">To: <?= htmlspecialchars((string) $note['phone']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="rounded-xl border border-dashed border-slate-200 py-10 text-center italic text-slate-400">No SMS notifications sent to this customer yet.</p>
<?php endif; ?>
