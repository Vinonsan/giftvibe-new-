<?php

declare(strict_types=1);

$homepageLimit = (int) ($homepageLimit ?? 6);
$featuredCount = count(array_filter($reviews, static fn (array $review): bool => $review['status'] === 'approved'));

$input = static function ($name, $label, $value = '', $type = 'text', $placeholder = ''): void {
    $inputName = $name; $inputId = $name; $inputLabel = $label; $inputValue = (string) $value;
    $inputType = $type; $inputPlaceholder = $placeholder; $inputHint = ''; $inputError = '';
    $inputSize = 'lg'; $inputState = 'default'; $inputRequired = $name === 'reviewer_name';
    $inputAutocomplete = ''; $inputReadonly = false; $inputDisabled = false; $inputLeadingIcon = '';
    $inputPrefix = ''; $inputTrailingIcon = ''; $inputSuffix = ''; $inputAttributes = [];
    $inputClass = ''; $inputWrapperClass = '';
    require BASE_PATH . '/resources/views/components/base/input.php';
};

$button = static function (string $label, array $options = []): void {
    $buttonLabel = $label; $buttonVariant = $options['variant'] ?? 'solid';
    $buttonColor = $options['color'] ?? 'primary'; $buttonSize = $options['size'] ?? 'md';
    $buttonType = $options['type'] ?? 'button'; $buttonHref = $options['href'] ?? '';
    $buttonName = $options['name'] ?? ''; $buttonValue = $options['value'] ?? ''; $buttonId = '';
    $buttonIcon = ''; $buttonIconTrailing = ''; $buttonIconOnly = false; $buttonFullWidth = false;
    $buttonDisabled = $options['disabled'] ?? false; $buttonLoading = false; $buttonOnclick = '';
    $buttonClass = $options['class'] ?? ''; $buttonAttributes = $options['attributes'] ?? [];
    require BASE_PATH . '/resources/views/components/base/button.php';
};
?>

<div class="space-y-5">
    <?php if ($flash): ?>
        <div class="rounded-xl px-4 py-3 text-sm <?= $flash['type'] === 'error' ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="flex flex-col gap-4 rounded-2xl border border-primary/10 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-secondary">Customer reviews</h1>
            <p class="mt-1 text-sm text-slate-500">View every review and select up to <?= $homepageLimit ?> for the homepage.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="rounded-xl border border-primary/15 bg-primary/5 px-3 py-2 text-sm font-bold text-primary"><?= $featuredCount ?> / <?= $homepageLimit ?> selected</span>
            <?php $button('Add review', ['size' => 'lg', 'attributes' => ['data-drawer-open' => 'review-drawer']]); ?>
        </div>
    </div>

    <?php
    $rows = [];
    foreach ($reviews as $review) {
        $id = (int) $review['id'];
        $featured = $review['status'] === 'approved';
        $editUrl = app_url('/admin/reviews?edit=' . $id);

        $toggleButton = '<form method="post" class="inline-flex">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken, ENT_QUOTES) . '">'
            . '<input type="hidden" name="id" value="' . $id . '">'
            . '<button type="submit" name="action" value="toggle_homepage" class="inline-flex items-center rounded-lg px-3 py-2 text-xs font-bold transition '
            . ($featured ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'border border-slate-200 bg-white text-slate-600 hover:border-primary/30 hover:text-primary') . '">'
            . ($featured ? 'On homepage' : 'Select for home') . '</button></form>';

        $editButton = '<a href="' . htmlspecialchars($editUrl, ENT_QUOTES) . '" title="Edit review" aria-label="Edit review" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 bg-white text-slate-500 transition hover:border-primary/20 hover:bg-primary/5 hover:text-primary">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 4.487ZM19.5 7.125 16.875 4.5M18 13.5V19.125A1.875 1.875 0 0 1 16.125 21H4.875A1.875 1.875 0 0 1 3 19.125V7.875A1.875 1.875 0 0 1 4.875 6H10.5"/></svg></a>';

        $deleteButton = '<form method="post" class="inline-flex" onsubmit="return confirm(\'Delete review?\')">'
            . '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrfToken, ENT_QUOTES) . '">'
            . '<input type="hidden" name="id" value="' . $id . '">'
            . '<button type="submit" name="action" value="delete" title="Delete review" aria-label="Delete review" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-white text-rose-500 transition hover:bg-rose-50">'
            . '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21L18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0H4.772m10.978 0V4.477c0-1.18-.91-2.164-2.09-2.201h-3.32c-1.18.037-2.09 1.022-2.09 2.201v.916"/></svg></button></form>';

        $source = $review['source'] === 'public' ? 'Customer submission' : 'Added by admin';
        $rows[] = [
            'reviewer' => '<strong class="text-secondary">' . htmlspecialchars((string) $review['reviewer_name']) . '</strong><small class="block text-slate-400">' . $source . '</small>',
            'review' => htmlspecialchars(mb_strimwidth((string) $review['review_text'], 0, 100, '…')),
            'rating' => '<span class="text-amber-400">' . str_repeat('★', (int) $review['rating']) . '</span>',
            'homepage' => $toggleButton,
            '_actions' => '<div class="flex justify-end gap-2">' . $editButton . $deleteButton . '</div>',
        ];
    }

    $tableId = 'reviews-table';
    $tableRows = $rows;
    $tableColumns = [
        ['key' => 'reviewer', 'label' => 'Reviewer', 'html' => true],
        ['key' => 'review', 'label' => 'Review'],
        ['key' => 'rating', 'label' => 'Rating', 'html' => true],
        ['key' => 'homepage', 'label' => 'Homepage', 'html' => true, 'sortable' => false],
        ['key' => '_actions', 'label' => 'Actions', 'html' => true, 'sortable' => false, 'align' => 'right'],
    ];
    $tablePerPage = 10; $tableZebra = false; $tableEmptyMessage = 'No reviews found.';
    require BASE_PATH . '/resources/views/components/base/datatable.php';
    ?>
</div>

<?php ob_start(); ?>
<form id="review-form" method="post" enctype="multipart/form-data" class="space-y-5">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int) $edit['id'] ?>"><?php endif; ?>
    <?php $input('reviewer_name', 'Reviewer name', $edit['reviewer_name'] ?? ''); ?>
    <div class="grid gap-4 sm:grid-cols-2">
        <?php $input('reviewer_email', 'Email', $edit['reviewer_email'] ?? '', 'email'); ?>
        <?php $input('reviewer_role', 'Role / location', $edit['reviewer_role'] ?? '', 'text', 'Colombo, Sri Lanka'); ?>
    </div>
    <?php
    $fileName = 'avatar'; $fileId = 'reviewer-avatar'; $fileLabel = 'Profile image';
    $fileHint = 'JPG, PNG or WebP · maximum 3MB'; $fileAccept = 'image/png,image/jpeg,image/webp';
    $fileRequired = false; $fileCurrentUrl = (string) ($edit['avatar_path'] ?? ''); $fileMultiple = false;
    require BASE_PATH . '/resources/views/components/base/file-input.php';
    ?>
    <?php $input('title', 'Review title', $edit['title'] ?? ''); ?>
    <label class="block space-y-1.5">
        <span class="text-sm font-medium text-secondary">Review *</span>
        <textarea required name="review_text" rows="5" class="w-full rounded-xl border border-primary/10 bg-white p-3 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"><?= htmlspecialchars((string) ($edit['review_text'] ?? '')) ?></textarea>
    </label>
    <div class="grid gap-4 sm:grid-cols-2 font-semibold">
        <?php
        $selectName = 'rating'; $selectId = 'review-rating';
        $selectOptions = ['5'=>'5 stars','4'=>'4 stars','3'=>'3 stars','2'=>'2 stars','1'=>'1 star'];
        $selectValue = (string) ($edit['rating'] ?? 5); $selectPlaceholder = 'Rating'; $selectLabel = 'Rating';
        $selectHint = ''; $selectError = ''; $selectSize = 'lg'; $selectState = 'default'; $selectMultiple = false;
        $selectRequired = true; $selectDisabled = false; $selectAttributes = []; $selectClass = '';
        require BASE_PATH . '/resources/views/components/base/select.php';
        $input('sort_order', 'Display order', $edit['sort_order'] ?? 0, 'number');
        ?>
    </div>
    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-primary/15 bg-primary/5 p-4">
        <input type="checkbox" name="show_on_homepage" value="1" class="mt-0.5 h-5 w-5 rounded border-primary/30 accent-primary" <?= ($edit['status'] ?? '') === 'approved' ? 'checked' : '' ?>>
        <span><strong class="block text-sm text-secondary">Show on homepage</strong><small class="mt-1 block text-slate-500">Admin can select up to <?= $homepageLimit ?> reviews.</small></span>
    </label>
</form>
<?php
$drawerBody = ob_get_clean();
ob_start();
$button('Cancel', ['variant' => 'outline', 'color' => 'secondary', 'size' => 'lg', 'attributes' => ['data-drawer-close' => '']]);
$button($edit ? 'Update review' : 'Create review', ['type' => 'submit', 'size' => 'lg', 'attributes' => ['form' => 'review-form']]);
$drawerFooter = ob_get_clean();
$drawerId = 'review-drawer'; $drawerSide = 'right'; $drawerSize = 'lg';
$drawerTitle = $edit ? 'Edit review' : 'Add review';
$drawerDescription = 'All reviews remain in the admin list. Homepage selection is limited to six.';
$drawerTrigger = '<span class="hidden"></span>'; $drawerStatic = false; $drawerCloseOnEsc = true;
$drawerShowCloseButton = true; $drawerOverlay = true;
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>
<?php if ($edit): ?>
    <script>document.addEventListener('DOMContentLoaded', function(){ document.querySelector('[data-drawer-open="review-drawer"]')?.click(); });</script>
<?php endif; ?>
