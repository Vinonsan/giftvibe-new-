<?php
declare(strict_types=1);
?>
<?php if ($addresses): ?>
    <div class="grid gap-4 sm:grid-cols-2">
        <?php foreach ($addresses as $addr): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <span class="rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-bold text-primary"><?= htmlspecialchars((string) ($addr['label'] ?? 'Address')) ?></span>
                    <?php if (!empty($addr['is_default'])): ?>
                        <span class="text-[10px] font-bold uppercase text-emerald-600">Default</span>
                    <?php endif; ?>
                </div>
                <p class="font-bold text-secondary"><?= htmlspecialchars((string) $addr['recipient_name']) ?></p>
                <p class="mt-1 text-sm text-slate-500"><?= htmlspecialchars((string) $addr['phone']) ?></p>
                <p class="mt-3 text-sm leading-relaxed text-secondary">
                    <?= htmlspecialchars((string) $addr['address_line_1']) ?><br>
                    <?php if (trim((string) ($addr['address_line_2'] ?? '')) !== ''): ?>
                        <?= htmlspecialchars((string) $addr['address_line_2']) ?><br>
                    <?php endif; ?>
                    <?= htmlspecialchars((string) $addr['city']) ?>, <?= htmlspecialchars((string) $addr['district']) ?>
                    <?php if (trim((string) ($addr['postal_code'] ?? '')) !== ''): ?>
                        · <?= htmlspecialchars((string) $addr['postal_code']) ?>
                    <?php endif; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="rounded-xl border border-dashed border-slate-200 py-10 text-center italic text-slate-400">No saved addresses for this customer.</p>
<?php endif; ?>
