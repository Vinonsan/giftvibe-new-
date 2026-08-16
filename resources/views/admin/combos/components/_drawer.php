<?php
declare(strict_types=1);
/**
 * Combos admin — 3-step wizard drawer.
 *
 *  Step 1  Basic Info        name, description, price, status
 *  Step 2  Products          products selector
 *  Step 3  Media & SEO       images, videos, seo details
 *
 * @var array|null $editCombo
 * @var array      $products
 * @var array      $selectedProducts
 * @var array      $comboImages
 * @var array      $comboVideos
 * @var string     $csrfToken
 */

$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$lsKey = 'gv_combo_draft_' . ($editCombo ? (int)$editCombo['id'] : 'new');

$productCostMap = [];
$productSellingMap = [];
foreach ($products as $product) {
    $productCostMap[(string) $product['id']] = (float) ($product['cost_price'] ?? 0);
    $productSellingMap[(string) $product['id']] = (float) ($product['base_price'] ?? 0);
}

$steps = [
    1 => ['label' => 'Basic Info',    'icon' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3ZM6 6h.008v.008H6V6Z'],
    2 => ['label' => 'Products & Pricing', 'icon' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z'],
    3 => ['label' => 'Media & SEO',   'icon' => 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z'],
];

ob_start();
?>
<!-- ══ Form ═══════════════════════════════════════════════════════════ -->
<form id="combo-form" method="post" action="<?= htmlspecialchars(app_url('/admin/combos')) ?>" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="action"     value="save">
    <?php if ($editCombo): ?>
        <input type="hidden" name="id" value="<?= (int)$editCombo['id'] ?>">
    <?php endif; ?>

    <!-- ── Steps ─────────────────────────── -->
    <?php require __DIR__ . '/steps/_step-1.php'; ?>
    <?php require __DIR__ . '/steps/_step-2.php'; ?>
    <?php require __DIR__ . '/steps/_step-3.php'; ?>
</form>

<!-- ══ JavaScript ═════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var TOTAL   = 3;
    var current = 1;
    var LS_KEY  = <?= json_encode($lsKey) ?>;
    var WIDTHS  = { 1:'33.33%', 2:'66.66%', 3:'100%' };
    var FC      = <?= json_encode($fc) ?>;
    var productPrices = <?= json_encode($productCostMap) ?>;
    var productSellingPrices = <?= json_encode($productSellingMap) ?>;

    /* ── Profit Calculation ────────────────────────── */
    function getSelectedTotals() {
        var totalCost = 0;
        var totalSelling = 0;
        document.querySelectorAll('#combo-selected-products input[name="product_ids[]"]').forEach(function (el) {
            var pid = el.value;
            totalCost += parseFloat(productPrices[pid]) || 0;
            totalSelling += parseFloat(productSellingPrices[pid]) || 0;
        });
        return { totalCost: totalCost, totalSelling: totalSelling };
    }

    function syncComboPrice() {
        var totals = getSelectedTotals();
        var otherEl = document.getElementById('field-other-cost');
        var otherCost = otherEl ? (parseFloat(otherEl.value) || 0) : 0;
        var sellingEl = document.getElementById('field-price');
        if (sellingEl) {
            sellingEl.value = (totals.totalSelling + otherCost).toFixed(2);
        }
    }

    function updatePricing() {
        var totals = getSelectedTotals();
        var totalCost = totals.totalCost;
        var totalSelling = totals.totalSelling;

        var costDisplay = document.getElementById('combo-total-cost');
        if (costDisplay) costDisplay.textContent = 'LKR ' + totalCost.toFixed(2);

        var sellingDisplay = document.getElementById('combo-total-selling');
        if (sellingDisplay) sellingDisplay.textContent = 'LKR ' + totalSelling.toFixed(2);

        var otherEl = document.getElementById('field-other-cost');
        var otherCost = otherEl ? (parseFloat(otherEl.value) || 0) : 0;

        var finalCost = totalCost + otherCost;
        var finalCostDisplay = document.getElementById('combo-total-final-cost');
        if (finalCostDisplay) finalCostDisplay.textContent = 'LKR ' + finalCost.toFixed(2);

        var sellingEl = document.getElementById('field-price');
        var sellingPrice = sellingEl ? (parseFloat(sellingEl.value) || 0) : 0;
        var profit = sellingPrice - finalCost;

        var profitDisplay = document.getElementById('combo-profit-display');
        if (profitDisplay) {
            profitDisplay.textContent = 'LKR ' + profit.toFixed(2);
            profitDisplay.className = 'mt-1 text-2xl font-black ' + (profit >= 0 ? 'text-emerald-600' : 'text-rose-500');
        }
    }

    document.getElementById('field-price')?.addEventListener('input', updatePricing);
    document.getElementById('field-other-cost')?.addEventListener('input', function () {
        syncComboPrice();
        updatePricing();
    });
    document.addEventListener('combo:products-change', function() { syncComboPrice(); updatePricing(); });

    /* ── localStorage helpers ──────────────────────── */
    function saveDraft() {
        try {
            var data = {};
            ['field-name','field-desc','field-price','field-other-cost'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) data[id] = el.value;
            });
            localStorage.setItem(LS_KEY, JSON.stringify(data));
        } catch(e) {}
    }

    function loadDraft() {
        try {
            var raw = localStorage.getItem(LS_KEY);
            if (!raw) return;
            var data = JSON.parse(raw);
            ['field-name','field-desc','field-price','field-other-cost'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el && data[id] !== undefined && el.value === '') el.value = data[id];
            });
            updatePricing();
        } catch(e) {}
    }

    function clearDraft() {
        try { localStorage.removeItem(LS_KEY); } catch(e) {}
    }

    /* ── Progress bar state ────────────────────────── */
    function updateStepper(n) {
        var bar = document.getElementById('gv-step-bar');
        if (bar) bar.style.width = WIDTHS[n] || '33%';

        var progress = bar ? bar.closest('[role="progressbar"]') : null;
        if (progress) progress.setAttribute('aria-valuenow', String(n));

        updateFooter(n);
        
        if (n === 2) {
            updatePricing();
        }
    }

    /* ── Panel switch ──────────────────────────────── */
    function goTo(n) {
        if (n < 1 || n > TOTAL) return;
        for (var i = 1; i <= TOTAL; i++) {
            var panel = document.getElementById('gv-step-' + i);
            if (panel) panel.classList.toggle('hidden', i !== n);
        }
        current = n;
        updateStepper(n);
    }

    /* ── Footer state ────────────────────────────── */
    var SAVE_LABEL = <?= json_encode($editCombo ? 'Save Changes' : 'Save Combo') ?>;
    function updateFooter(n) {
        var back = document.getElementById('gv-btn-back');
        if (back) back.classList.toggle('hidden', n === 1);

        var btn = document.getElementById('gv-btn-primary');
        if (!btn) return;
        if (n === TOTAL) {
            btn.type = 'submit';
            btn.setAttribute('form', 'combo-form');
            btn.innerHTML =
                '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">' +
                '<path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg> ' +
                SAVE_LABEL;
        } else {
            btn.type = 'button';
            btn.removeAttribute('form');
            btn.innerHTML =
                'Save & Continue ' +
                '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">' +
                '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>';
        }
    }

    function toast(msg, type) {
        if (window.GiftVibeToast) window.GiftVibeToast.show(msg, type || 'success');
    }

    function validateAllSteps() {
        for (var step = 1; step <= TOTAL; step++) {
            if (!validateStep(step)) {
                goTo(step);
                return false;
            }
        }
        return true;
    }

    /* ── Step validation ─────────────────────────── */
    function showError(el, msg) {
        el.classList.add('border-rose-400', 'ring-2', 'ring-rose-400/20');
        var err = el.parentElement.querySelector('.gv-field-err');
        if (!err) {
            err = document.createElement('p');
            err.className = 'gv-field-err mt-1 text-xs text-rose-600 font-semibold';
            el.parentElement.appendChild(err);
        }
        err.textContent = msg;
        if(el.focus) el.focus();
    }
    function clearErrors(stepEl) {
        stepEl.querySelectorAll('.border-rose-400').forEach(function(el) {
            el.classList.remove('border-rose-400', 'ring-2', 'ring-rose-400/20');
        });
        stepEl.querySelectorAll('.gv-field-err').forEach(function(el) { el.remove(); });
    }
    function validateStep(n) {
        var stepEl = document.getElementById('gv-step-' + n);
        if (!stepEl) return true;
        clearErrors(stepEl);
        var ok = true;
        if (n === 1) {
            var nameEl = document.getElementById('field-name');
            if (nameEl && !nameEl.value.trim()) {
                showError(nameEl, 'Combo name is required.');
                ok = false;
            }
        }
        if (n === 2) {
            var chosen = stepEl.querySelectorAll('#combo-selected-products input[name="product_ids[]"]').length > 0;
            if (!chosen) { toast('Please select at least one product.', 'error'); ok = false; }
            var priceEl = document.getElementById('field-price');
            if (priceEl && (!priceEl.value.trim() || parseFloat(priceEl.value) <= 0)) {
                showError(priceEl, 'Valid selling price is required.');
                ok = false;
            }
        }
        if (n === 3) {
            var isEdit = !!document.querySelector('#combo-form input[name="id"]');
            var fileInput = document.getElementById('combo-images');
            var existingImages = stepEl.querySelectorAll('input[name="delete_images[]"]').length;
            var deletedImages = stepEl.querySelectorAll('input[name="delete_images[]"]:checked').length;
            var hasNewFiles = fileInput && fileInput.files && fileInput.files.length > 0;
            var hasRemaining = (existingImages - deletedImages) > 0;
            if ((!isEdit && !hasNewFiles) || (isEdit && !hasRemaining && !hasNewFiles)) {
                var fileTrigger = stepEl.querySelector('[data-file-input] label[for="combo-images"]');
                if (fileTrigger) showError(fileTrigger, 'Upload at least one combo image.');
                ok = false;
            }
        }
        return ok;
    }

    /* ── Delegation ────────────────────────────────── */
    document.addEventListener('click', function (e) {
        var backBtn = e.target.closest('#gv-btn-back');
        if (backBtn) { goTo(current - 1); return; }

        var primBtn = e.target.closest('#gv-btn-primary');
        if (primBtn && primBtn.type === 'button') {
            if (!validateStep(current)) {
                toast('Please complete the required fields on this step.', 'error');
                return;
            }
            saveDraft();
            goTo(current + 1);
            toast('Step saved.', 'success');
            return;
        }

        if (primBtn && primBtn.type === 'submit') {
            e.preventDefault();
            if (!validateAllSteps()) {
                toast('Please complete all required fields before saving.', 'error');
                return;
            }
            var form = document.getElementById('combo-form');
            if (!form) return;
            clearDraft();
            if (form.requestSubmit) form.requestSubmit(primBtn);
            else form.submit();
            return;
        }
    });

    /* ── Video links ───────────────────────────────── */
    var videoList = document.querySelector('[data-video-list]');
    function makeVideoRow() {
        var row = document.createElement('div');
        row.dataset.videoRow = '';
        row.className = 'flex gap-2';
        row.innerHTML = '<input type="url" name="video_urls[]" placeholder="https://www.youtube.com/watch?v=…" class="' + FC + '">' +
            '<button type="button" data-remove-video class="shrink-0 rounded-xl border border-rose-200 px-3 text-rose-500 hover:bg-rose-50 cursor-pointer">&times;</button>';
        return row;
    }
    function bindVideoRemove(btn) {
        btn.addEventListener('click', function () {
            var rows = videoList ? videoList.querySelectorAll('[data-video-row]') : [];
            if (rows.length > 1) btn.closest('[data-video-row]').remove();
            else { var inp = btn.closest('[data-video-row]').querySelector('input'); if(inp) inp.value = ''; }
        });
    }
    videoList && videoList.querySelectorAll('[data-remove-video]').forEach(bindVideoRemove);
    document.querySelector('[data-add-video]')?.addEventListener('click', function () {
        var row = makeVideoRow();
        videoList.appendChild(row);
        bindVideoRemove(row.querySelector('[data-remove-video]'));
    });

    document.getElementById('combo-form')?.addEventListener('submit', function (e) {
        if (!validateAllSteps()) {
            e.preventDefault();
            toast('Please complete all required fields before saving.', 'error');
            return;
        }
        clearDraft();
    });

    loadDraft();
    goTo(1);
    
    // Initial pricing sync
    setTimeout(updatePricing, 100);
});
</script>
<?php
$stepperDrawerBody = (string) ob_get_clean();
$stepperDrawerId = 'combo-drawer';
$stepperDrawerSteps = array_values($steps);
$stepperDrawerTitle = $editCombo ? 'Edit combo' : 'Add combo';
$stepperDrawerDescription = '';
$stepperDrawerPrefix = 'gv';
$stepperDrawerFormId = 'combo-form';
$stepperDrawerSubmitLabel = $editCombo ? 'Save Changes' : 'Save Combo';
$stepperDrawerTrigger = '<span class="hidden" aria-hidden="true"></span>';
$stepperDrawerSize = 'full';
$stepperDrawerExternalNavigation = true;
$stepperDrawerShowHeader = true;
require BASE_PATH . '/resources/views/components/drawer/stepper-drawer.php';
?>
<script>
(function () {
    var h3 = document.querySelector('#combo-drawer aside h3');
    if (h3) h3.id = 'combo-drawer-title';
    var footer = document.querySelector('#combo-drawer aside > div:last-child');
    if (footer) {
        footer.classList.remove('justify-end');
        footer.classList.add('w-full', 'justify-between');
    }
})();
</script>
<?php if ($editCombo): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('[data-drawer-open="combo-drawer"]')?.click();
});
</script>
<?php endif; ?>
