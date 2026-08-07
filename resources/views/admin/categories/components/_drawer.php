<?php
declare(strict_types=1);
/**
 * Categories admin — 3-step wizard drawer.
 *
 * @var array|null $editCategory
 * @var array      $categories
 * @var string     $csrfToken
 */

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$lsKey = 'gc_category_draft_' . ($editCategory ? (int) $editCategory['id'] : 'new');

$steps = [
    1 => ['label' => 'Basic Info'],
    2 => ['label' => 'Image'],
    3 => ['label' => 'Settings'],
];

ob_start();
?>
<form id="category-form" method="post" action="/admin/categories" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="action" value="save">
    <?php if ($editCategory): ?>
        <input type="hidden" name="id" value="<?= (int) $editCategory['id'] ?>">
    <?php endif; ?>
    <?php require __DIR__ . '/steps/_step-1.php'; ?>
    <?php require __DIR__ . '/steps/_step-2.php'; ?>
    <?php require __DIR__ . '/steps/_step-3.php'; ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    var TOTAL = 3, current = 1;
    var LS_KEY = <?= json_encode($lsKey) ?>;
    var WIDTHS = { 1: '33%', 2: '66%', 3: '100%' };
    var SAVE_LABEL = <?= json_encode($editCategory ? 'Save Changes' : 'Save Category') ?>;

    function saveDraft() {
        try {
            var data = {};
            ['field-cat-name', 'field-cat-link', 'field-cat-desc', 'field-cat-order'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) data[id] = el.value;
            });
            localStorage.setItem(LS_KEY, JSON.stringify(data));
        } catch (e) {}
    }
    function loadDraft() {
        try {
            var raw = localStorage.getItem(LS_KEY);
            if (!raw) return;
            var data = JSON.parse(raw);
            ['field-cat-name', 'field-cat-link', 'field-cat-desc', 'field-cat-order'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el && data[id] !== undefined && el.value === '') el.value = data[id];
            });
        } catch (e) {}
    }
    function clearDraft() { try { localStorage.removeItem(LS_KEY); } catch (e) {} }
    function toast(msg, type) { if (window.GiftVibeToast) window.GiftVibeToast.show(msg, type || 'success'); }

    function updateStepper(n) {
        var bar = document.getElementById('gc-step-bar');
        if (bar) bar.style.width = WIDTHS[n] || '33%';
        var progress = bar ? bar.closest('[role="progressbar"]') : null;
        if (progress) progress.setAttribute('aria-valuenow', String(n));
        updateFooter(n);
    }
    function goTo(n) {
        if (n < 1 || n > TOTAL) return;
        for (var i = 1; i <= TOTAL; i++) {
            var panel = document.getElementById('gc-step-' + i);
            if (panel) panel.classList.toggle('hidden', i !== n);
        }
        current = n;
        updateStepper(n);
    }
    function updateFooter(n) {
        var back = document.getElementById('gc-btn-back');
        if (back) back.classList.toggle('hidden', n === 1);
        var btn = document.getElementById('gc-btn-primary');
        if (!btn) return;
        if (n === TOTAL) {
            btn.type = 'submit';
            btn.setAttribute('form', 'category-form');
            btn.innerHTML = '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg> ' + SAVE_LABEL;
        } else {
            btn.type = 'button';
            btn.removeAttribute('form');
            btn.innerHTML = 'Save & Continue <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>';
        }
    }
    function showError(el, msg) {
        el.classList.add('border-rose-400', 'ring-2', 'ring-rose-400/20');
        var err = el.parentElement.querySelector('.gc-field-err');
        if (!err) {
            err = document.createElement('p');
            err.className = 'gc-field-err mt-1 text-xs text-rose-600 font-semibold';
            el.parentElement.appendChild(err);
        }
        err.textContent = msg;
        el.focus();
    }
    function clearErrors(stepEl) {
        stepEl.querySelectorAll('.border-rose-400').forEach(function (el) {
            el.classList.remove('border-rose-400', 'ring-2', 'ring-rose-400/20');
        });
        stepEl.querySelectorAll('.gc-field-err').forEach(function (el) { el.remove(); });
    }
    function validateStep(n) {
        var stepEl = document.getElementById('gc-step-' + n);
        if (!stepEl) return true;
        clearErrors(stepEl);
        var ok = true;
        if (n === 1) {
            var nameEl = document.getElementById('field-cat-name');
            var linkEl = document.getElementById('field-cat-link');
            if (nameEl && !nameEl.value.trim()) { showError(nameEl, 'Category name is required.'); ok = false; }
            if (linkEl && !linkEl.value.trim()) { showError(linkEl, 'Category link is required.'); ok = false; }
        }
        if (n === 2) {
            var isEdit = !!document.querySelector('#category-form input[name="id"]');
            var fileInput = document.getElementById('category-image');
            var hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
            var hasPreview = stepEl.querySelector('[data-file-preview-wrap]') && !stepEl.querySelector('[data-file-preview-wrap]').classList.contains('hidden');
            if (!isEdit && !hasFile) {
                var fileTrigger = stepEl.querySelector('[data-file-input] label[for="category-image"]');
                if (fileTrigger) showError(fileTrigger, 'Category image is required.');
                ok = false;
            } else if (isEdit && !hasFile && !hasPreview) {
                var fileTrigger2 = stepEl.querySelector('[data-file-input] label[for="category-image"]');
                if (fileTrigger2) showError(fileTrigger2, 'Category image is required.');
                ok = false;
            }
        }
        return ok;
    }
    function validateAllSteps() {
        for (var step = 1; step <= TOTAL; step++) {
            if (!validateStep(step)) { goTo(step); return false; }
        }
        return true;
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('#gc-btn-back')) { goTo(current - 1); return; }
        var primBtn = e.target.closest('#gc-btn-primary');
        if (primBtn && primBtn.type === 'button') {
            if (!validateStep(current)) { toast('Please complete the required fields on this step.', 'error'); return; }
            saveDraft(); goTo(current + 1); toast('Step saved.', 'success'); return;
        }
        if (primBtn && primBtn.type === 'submit') {
            e.preventDefault();
            if (!validateAllSteps()) { toast('Please complete all required fields before saving.', 'error'); return; }
            var form = document.getElementById('category-form');
            if (!form) return;
            clearDraft();
            if (form.requestSubmit) form.requestSubmit(primBtn); else form.submit();
        }
    });

    document.getElementById('category-form')?.addEventListener('submit', function (e) {
        if (!validateAllSteps()) { e.preventDefault(); toast('Please complete all required fields before saving.', 'error'); return; }
        clearDraft();
    });

    loadDraft();
    goTo(1);
});
</script>
<?php
$stepperDrawerBody = (string) ob_get_clean();
$stepperDrawerId = 'category-drawer';
$stepperDrawerSteps = array_values($steps);
$stepperDrawerTitle = $editCategory ? 'Edit category' : 'Add category';
$stepperDrawerDescription = '';
$stepperDrawerPrefix = 'gc';
$stepperDrawerFormId = 'category-form';
$stepperDrawerSubmitLabel = $editCategory ? 'Save Changes' : 'Save Category';
$stepperDrawerTrigger = '<span class="hidden" aria-hidden="true"></span>';
$stepperDrawerSize = 'lg';
$stepperDrawerExternalNavigation = true;
$stepperDrawerShowHeader = true;
require BASE_PATH . '/resources/views/components/drawer/stepper-drawer.php';
?>
<script>
(function () {
    var footer = document.querySelector('#category-drawer aside > div:last-child');
    if (footer) {
        footer.classList.remove('justify-end');
        footer.classList.add('w-full', 'justify-between');
    }
})();
</script>
<?php if ($editCategory): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('[data-drawer-open="category-drawer"]')?.click();
});
</script>
<?php endif; ?>
