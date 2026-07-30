<?php
$groups = [];
foreach (($settings ?? []) as $setting) {
    $groups[$setting['setting_group'] ?? 'general'][] = $setting;
}
?>
<div class="space-y-6">
    <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
        <p class="text-xs font-bold uppercase tracking-wider text-primary">Store configuration</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Settings</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Tune public brand, commerce, upload, and operational defaults from one simple settings panel.</p>
        <?php if (!empty($message)): ?><div class="mt-4 rounded-card border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700"><?= e($message) ?></div><?php endif; ?>
    </section>
    <form method="POST" action="<?= e(url('/admin/settings')) ?>" class="space-y-5">
        <?= csrf_field() ?>
        <?php foreach ($groups as $group => $items): ?>
            <section class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                <h2 class="text-lg font-extrabold text-slate-950"><?= e(ucwords(str_replace('_', ' ', $group))) ?></h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <?php foreach ($items as $item): ?>
                        <label class="block">
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500"><?= e($item['setting_key']) ?></span>
                            <input name="settings[<?= e($item['setting_key']) ?>]" value="<?= e($item['setting_value']) ?>" class="w-full rounded-button border border-slate-300 px-3 py-2 text-sm">
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
        <button class="rounded-button bg-primary px-5 py-3 text-sm font-bold text-white">Save settings</button>
    </form>
</div>
