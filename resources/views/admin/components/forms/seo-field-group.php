<?php
/**
 * Variables:
 * @var string|null $metaTitle
 * @var string|null $metaDescription
 * @var string|null $canonicalUrl
 */
?>
<div class="border border-slate-200 rounded-card p-4 space-y-4 bg-slate-50/50">
    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">SEO Configurations</h3>
    <?php component('admin/components/forms/form-field', [
        'label' => 'Meta Title',
        'name' => 'meta_title',
        'id' => 'meta_title',
        'value' => $metaTitle ?? '',
        'helpText' => 'Recommended: Under 60 characters.'
    ]); ?>
    <?php component('admin/components/forms/textarea-field', [
        'label' => 'Meta Description',
        'name' => 'meta_description',
        'id' => 'meta_description',
        'value' => $metaDescription ?? '',
        'rows' => 3,
        'helpText' => 'Recommended: Under 160 characters.'
    ]); ?>
    <?php component('admin/components/forms/form-field', [
        'label' => 'Canonical URL',
        'name' => 'canonical_url',
        'id' => 'canonical_url',
        'value' => $canonicalUrl ?? '',
        'placeholder' => 'https://giftvibelk.com/...'
    ]); ?>
</div>