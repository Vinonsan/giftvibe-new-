<?php
/**
 * Variables:
 * @var string $id
 * @var string $title
 * @var string $message
 * @var string $actionUrl Form submission URL
 * @var string|null $confirmLabel
 */
?>
<?php component('admin/components/common/modal', [
    'id' => $id,
    'title' => $title,
    'content' => '<p class="text-slate-600">' . e($message) . '</p>',
    'footer' => '
        <button type="button" class="px-4 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 text-sm font-semibold rounded-button" onclick="closeModal(\'#' . e($id) . '\')">Cancel</button>
        <form action="' . e($actionUrl) . '" method="POST" class="inline">
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-button">' . e($confirmLabel ?? 'Confirm') . '</button>
        </form>
    '
]); ?>