<?php
/**
 * Variables:
 * @var array $headers Columns titles: ['Name', 'Price']
 * @var array $rows Grid data: [['Cell 1', 'Cell 2']]
 */
?>
<div class="overflow-x-auto border border-slate-200 rounded-card shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 bg-white text-sm">
        <thead class="bg-slate-50 text-slate-700 text-xs font-bold uppercase tracking-wider">
            <tr>
                <?php foreach ($headers as $header): ?>
                    <th scope="col" class="px-6 py-3 text-left"><?= e($header) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-600">
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="<?= count($headers) ?>" class="px-6 py-8 text-center text-slate-400">No data available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <?php foreach ($row as $cell): ?>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $cell ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>