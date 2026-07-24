<?php
/**
 * Variables:
 * @var array $errors List of string errors
 */
?>
<?php if (!empty($errors)): ?>
    <div class="p-4 bg-red-50 border border-red-200 rounded-card text-red-800 space-y-2">
        <h4 class="text-sm font-bold flex items-center gap-2">
            <?php component('admin/components/common/icon', ['name' => 'warning', 'size' => 'sm']); ?>
            Please correct the following errors:
        </h4>
        <ul class="list-disc pl-5 text-xs space-y-1 font-semibold">
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>