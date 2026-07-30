<?php
/**
 * Custom Select Dropdown Component
 *
 * Variables:
 * @var string $name        - Form input name
 * @var array  $options     - [['value' => '...', 'label' => '...'], ...]
 * @var string $selected    - Currently selected value
 * @var string $placeholder - Placeholder text (optional)
 * @var string $class       - Additional CSS classes (optional)
 * @var string $id          - Element ID (optional)
 * @var string $aria_label  - Accessibility label (optional)
 * @var string $data_attrs  - Additional data attributes as HTML string (optional)
 */
$id = $id ?? 'custom-select-' . bin2hex(random_bytes(4));
$selected = $selected ?? '';
$placeholder = $placeholder ?? 'Select...';
$class = $class ?? '';
$aria_label = $aria_label ?? '';
$data_attrs = $data_attrs ?? '';
?>
<div
    class="custom-select relative <?= e($class) ?>"
    data-custom-select
    data-name="<?= e($name) ?>"
    id="<?= e($id) ?>"
    role="combobox"
    aria-expanded="false"
    aria-haspopup="listbox"
    aria-label="<?= e($aria_label ?: $placeholder) ?>"
    tabindex="0"
    <?= $data_attrs ?>
>
    <input type="hidden" name="<?= e($name) ?>" value="<?= e($selected) ?>" data-custom-select-value>

    <button
        type="button"
        class="custom-select-trigger min-h-11 w-full rounded-button border border-slate-200 bg-white px-4 pr-10 text-sm font-bold text-primary-900 text-left outline-none transition duration-200 focus:border-primary-900 focus:ring-2 focus:ring-primary-100"
        data-custom-select-trigger
        aria-labelledby="<?= e($id) ?>-label"
    >
        <span class="pointer-events-none" data-custom-select-text><?= e($placeholder) ?></span>
        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
        </span>
    </button>

    <ul
        class="custom-select-options absolute z-50 mt-1 hidden w-full rounded-button border border-slate-200 bg-white py-1 shadow-lg"
        role="listbox"
        aria-label="<?= e($aria_label ?: $placeholder) ?>"
        data-custom-select-options
    >
        <?php foreach ($options as $opt): ?>
            <li
                role="option"
                class="custom-select-option cursor-pointer px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-primary-50 hover:text-primary-900 aria-selected:bg-primary-50 aria-selected:text-primary-900 aria-selected:font-bold"
                data-value="<?= e($opt['value'] ?? '') ?>"
                aria-selected="<?= ((string) ($opt['value'] ?? '')) === (string) $selected ? 'true' : 'false' ?>"
                tabindex="-1"
            ><?= e($opt['label'] ?? $opt['value'] ?? '') ?></li>
        <?php endforeach; ?>
    </ul>
</div>
