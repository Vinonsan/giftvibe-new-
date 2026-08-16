<?php
declare(strict_types=1);

$renderInput = static function(string $name, string $label, string $value = '', string $type = 'text', bool $required = false): void {
    $inputType = $type;
    $inputName = $name;
    $inputId = $name;
    $inputValue = $value;
    $inputPlaceholder = '';
    $inputLabel = $label;
    $inputHint = '';
    $inputError = '';
    $inputSize = 'lg';
    $inputState = 'default';
    $inputRequired = $required;
    $inputAutocomplete = '';
    $inputReadonly = false;
    $inputDisabled = false;
    $inputLeadingIcon = '';
    $inputPrefix = '';
    $inputTrailingIcon = '';
    $inputSuffix = '';
    $inputAttributes = $type === 'number' ? ['min' => '0'] : [];
    $inputClass = '';
    $inputWrapperClass = '';
    require BASE_PATH . '/resources/views/components/base/input.php';
};
?>
<div class="space-y-5">
    <?php if ($flash): ?>
        <div class="rounded-xl px-4 py-3 text-sm <?= $flash['type'] === 'error' ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="flex items-center justify-between rounded-2xl border border-primary/10 bg-white p-5 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-secondary">FAQs</h1>
            <p class="mt-1 text-sm text-slate-500">Manage homepage questions and SEO-ready answers.</p>
        </div>
        <button data-drawer-open="faq-drawer" class="rounded-lg bg-primary px-5 py-2.5 font-semibold text-white">Add FAQ</button>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-wrap items-center gap-3">
            <div class="w-full max-w-xs">
                <?php
                $inputName = 'faq_search';
                $inputId = 'faq-search';
                $inputValue = '';
                $inputType = 'search';
                $inputPlaceholder = 'Search FAQs...';
                $inputLabel = '';
                $inputHint = '';
                $inputError = '';
                $inputSize = 'md';
                $inputState = 'default';
                $inputRequired = false;
                $inputAutocomplete = '';
                $inputReadonly = false;
                $inputDisabled = false;
                $inputLeadingIcon = '<svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m20 20-3.5-3.5M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>';
                $inputPrefix = '';
                $inputTrailingIcon = '';
                $inputSuffix = '';
                $inputAttributes = ['data-datatable-search' => '', 'data-datatable-target' => 'faqs-table'];
                $inputClass = '';
                require BASE_PATH . '/resources/views/components/base/input.php';
                ?>
            </div>
            <div class="w-full max-w-[200px]">
                <?php
                $selectName = 'faq_page_filter';
                $selectId = 'faq-page-filter';
                $selectOptions = [
                    'home' => 'Home Page',
                    'about' => 'About Page',
                    'contact' => 'Contact Page',
                    'services' => 'Services Page'
                ];
                $selectValue = '';
                $selectPlaceholder = 'All Pages';
                $selectLabel = '';
                $selectHint = '';
                $selectError = '';
                $selectSize = 'md';
                $selectState = 'default';
                $selectMultiple = false;
                $selectRequired = false;
                $selectDisabled = false;
                $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'faqs-table', 'data-filter-key' => 'page'];
                $selectClass = '';
                require BASE_PATH . '/resources/views/components/base/select.php';
                ?>
            </div>
            <button type="button" data-datatable-reset data-datatable-target="faqs-table" class="rounded-xl border border-primary/10 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-primary/5 hover:text-primary">Reset</button>
        </div>
    </div>

    <?php
    $rows = [];
    $pageLabels = [
        'home' => 'Home Page',
        'about' => 'About Page',
        'contact' => 'Contact Page',
        'services' => 'Services Page'
    ];

    foreach ($faqs as $f) {
        $actions = '<div class="flex justify-end gap-2">'
            . '<a href="/admin/faqs?edit=' . (int)$f['id'] . '" title="View FAQ" aria-label="View FAQ" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 bg-white text-slate-500 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg></a>'
            . '<form method="post" onsubmit="return confirm(\'Delete FAQ?\')">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken) . '">'
            . '<input type="hidden" name="id" value="' . (int)$f['id'] . '">'
            . '<button type="submit" name="action" value="delete" title="Delete FAQ" aria-label="Delete FAQ" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-white text-rose-500 transition hover:bg-rose-50 hover:text-rose-600">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .563c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0V4.477c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg></button>'
            . '</form></div>';

        $rows[] = [
            'question' => $f['question'],
            'page' => $pageLabels[$f['category']] ?? ucfirst($f['category']),
            'status' => ucfirst($f['status']),
            'order' => $f['sort_order'],
            '_actions' => $actions
        ];
    }

    $tableId = 'faqs-table';
    $tableRows = $rows;
    $tableColumns = [
        ['key' => 'question', 'label' => 'Question'],
        ['key' => 'page', 'label' => 'Page'],
        ['key' => 'status', 'label' => 'Status'],
        ['key' => 'order', 'label' => 'Order', 'type' => 'number'],
        ['_actions' => '', 'key' => '_actions', 'label' => 'Action', 'html' => true, 'sortable' => false, 'align' => 'right']
    ];
    $tablePerPage = 10;
    $tableZebra = false;
    $tableEmptyMessage = 'No FAQs found.';
    require BASE_PATH . '/resources/views/components/base/datatable.php';
    ?>
</div>

<?php ob_start(); ?>
<form id="faq-form" method="post" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
    <?php endif; ?>

    <?php $renderInput('question', 'Question', $edit['question'] ?? '', 'text', true); ?>

    <label class="block space-y-1.5">
        <span class="text-sm font-medium text-secondary">Answer <span class="text-rose-500">*</span></span>
        <textarea required name="answer" rows="7" class="w-full rounded-xl border border-primary/10 bg-white p-3.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><?= htmlspecialchars((string)($edit['answer'] ?? '')) ?></textarea>
    </label>

    <div class="grid gap-4 sm:grid-cols-2 items-end">
        <div>
            <?php
            $selectName = 'category';
            $selectId = 'faq-category';
            $selectOptions = [
                'home' => 'Home Page',
                'about' => 'About Page',
                'contact' => 'Contact Page',
                'services' => 'Services Page'
            ];
            $selectValue = $edit['category'] ?? 'home';
            $selectLabel = 'Page Location';
            $selectRequired = true;
            $selectPlaceholder = '';
            $selectHint = '';
            $selectError = '';
            $selectSize = 'md';
            $selectState = 'default';
            $selectMultiple = false;
            $selectDisabled = false;
            $selectAttributes = [];
            $selectClass = '';
            require BASE_PATH . '/resources/views/components/base/select.php';
            ?>
        </div>
        <?php $renderInput('sort_order', 'Order', (string)($edit['sort_order'] ?? 0), 'number'); ?>
    </div>

    <?php
    $toggleName = 'status';
    $toggleId = 'faq-status';
    $toggleChecked = !$edit || $edit['status'] === 'active';
    $toggleLabel = 'Active';
    $toggleHint = 'Show this FAQ publicly.';
    $toggleError = '';
    $toggleSize = 'md';
    $toggleColor = 'primary';
    $toggleState = 'default';
    $toggleRequired = false;
    $toggleDisabled = false;
    $toggleAttributes = ['value' => 'active'];
    $toggleClass = '';
    require BASE_PATH . '/resources/views/components/base/toggle.php';
    ?>
</form>
<?php
$drawerBody = ob_get_clean();
$drawerFooter = '<button data-drawer-close class="rounded-lg border border-primary/10 px-5 py-2.5 font-semibold hover:bg-primary/5">Cancel</button>'
    . '<button form="faq-form" type="submit" class="rounded-lg bg-primary px-5 py-2.5 font-semibold text-white hover:bg-secondary"> ' . ($edit ? 'Update FAQ' : 'Create FAQ') . '</button>';
$drawerId = 'faq-drawer';
$drawerSide = 'right';
$drawerSize = 'lg';
$drawerTitle = $edit ? 'View & edit FAQ' : 'Add FAQ';
$drawerDescription = 'Write concise, helpful answers for customers and search engines.';
$drawerTrigger = '<span class="hidden"></span>';
$drawerStatic = false;
$drawerCloseOnEsc = true;
$drawerShowCloseButton = true;
$drawerOverlay = true;
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>
<?php if ($edit): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => document.querySelector('[data-drawer-open="faq-drawer"]')?.click())
    </script>
<?php endif; ?>
