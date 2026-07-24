<?php
/**
 * Variables:
 * @var array $headers Columns definitions: [['key' => 'name', 'label' => 'Name', 'sortable' => true]]
 * @var array $rows Data rows matching keys
 * @var string|null $editUrlPrefix Prefix for row editing: /admin/products/edit/
 * @var string|null $deleteUrlPrefix Prefix for row deletion
 */
?>
<div class="overflow-x-auto border border-slate-200 rounded-card bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-slate-700 text-xs font-bold uppercase tracking-wider">
            <tr>
                <th scope="col" class="px-6 py-3 text-left w-10">
                    <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500" data-select-all>
                </th>
                <?php foreach ($headers as $header): ?>
                    <th scope="col" class="px-6 py-3 text-left">
                        <div class="flex items-center gap-1">
                            <span><?= e($header['label']) ?></span>
                            <?php if ($header['sortable'] ?? false): ?>
                                <?php component('admin/components/common/icon', ['name' => 'chevron-down', 'size' => '2xs', 'class' => 'opacity-50']); ?>
                            <?php endif; ?>
                        </div>
                    </th>
                <?php endforeach; ?>
                <th scope="col" class="px-6 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-600">
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="<?= count($headers) + 2 ?>" class="px-6 py-8 text-center text-slate-400">No data records found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): 
                    $rowId = $row['id'] ?? 0;
                ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500" value="<?= e($rowId) ?>">
                        </td>
                        <?php foreach ($headers as $header): 
                            $key = $header['key'];
                            $cellVal = $row[$key] ?? '';
                        ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($key === 'status'): ?>
                                    <?php component('admin/components/tables/status-cell', ['status' => $cellVal]); ?>
                                <?php else: ?>
                                    <?= e($cellVal) ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <?php component('admin/components/tables/table-row-actions', [
                                'rowId' => $rowId,
                                'editUrl' => !empty($editUrlPrefix) ? $editUrlPrefix . $rowId : null,
                                'deleteUrl' => !empty($deleteUrlPrefix) ? $deleteUrlPrefix . $rowId : null
                            ]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>