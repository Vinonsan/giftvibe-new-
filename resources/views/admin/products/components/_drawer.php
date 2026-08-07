<?php
declare(strict_types=1);
/**
 * Products admin — 5-step wizard drawer.
 *
 *  Step 1  Basic Info        name · descriptions · categories
 *  Step 2  Keywords          hashtag tag editor
 *  Step 3  Images            upload + saved images grid + alt text
 *  Step 4  Social Links      YouTube videos + social media platform links
 *  Step 5  Pricing           price · cost · profit · stock · flags
 *
 * Each step's field values are persisted to localStorage on "Next" so
 * the drawer can be closed and reopened without losing work.
 *
 * @var array|null $editProduct
 * @var array      $categories
 * @var array      $editCategoryIds
 * @var array      $productVideos
 * @var array      $productSocialLinks   array of ['platform','label','url','sort_order']
 * @var array      $secondaryImages
 * @var string     $csrfToken
 */

$productSocialLinks = $productSocialLinks ?? [];
$fc = 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white';
$lsKey = 'gv_product_draft_' . ($editProduct ? (int)$editProduct['id'] : 'new');

$steps = [
    1 => ['label' => 'Basic Info',    'icon' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3ZM6 6h.008v.008H6V6Z'],
    2 => ['label' => 'Keywords',      'icon' => 'M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5-3.9 19.5m-2.1-19.5-3.9 19.5'],
    3 => ['label' => 'Images',        'icon' => 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z'],
    4 => ['label' => 'Social Links',  'icon' => 'M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244'],
    5 => ['label' => 'Pricing',       'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
];

ob_start();
?>
<!-- ══ Form ═══════════════════════════════════════════════════════════ -->
<form id="product-form" method="post" action="/admin/products" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="action"     value="save">
    <?php if ($editProduct): ?>
        <input type="hidden" name="id" value="<?= (int)$editProduct['id'] ?>">
    <?php endif; ?>

    <!-- ── Step 1 · Basic Info ─────────────────────────── -->
    <?php require __DIR__ . '/steps/_step-1.php'; ?>
    <?php require __DIR__ . '/steps/_step-2.php'; ?>
    <?php require __DIR__ . '/steps/_step-3.php'; ?>
    <?php require __DIR__ . '/steps/_step-4.php'; ?>
    <?php require __DIR__ . '/steps/_step-5.php'; ?>
</form>

<!-- ══ JavaScript ═════════════════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var TOTAL   = 5;
    var current = 1;
    var LS_KEY  = <?= json_encode($lsKey) ?>;
    var WIDTHS  = { 1:'20%', 2:'40%', 3:'60%', 4:'80%', 5:'100%' };
    var LABELS  = { 1:'Basic Info', 2:'Keywords', 3:'Images', 4:'Social Links', 5:'Pricing' };
    var FC      = <?= json_encode($fc) ?>;

    /* ── localStorage helpers ──────────────────────── */
    function saveDraft() {
        try {
            var data = {};
            ['field-name','field-sku','field-short-desc','field-desc','selling-price','buying-price'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) data[id] = el.value;
            });
            var kw = document.getElementById('gv-keywords-hidden');
            if (kw) data['keywords'] = kw.value;
            localStorage.setItem(LS_KEY, JSON.stringify(data));
        } catch(e) {}
    }

    function loadDraft() {
        try {
            var raw = localStorage.getItem(LS_KEY);
            if (!raw) return;
            var data = JSON.parse(raw);
            ['field-name','field-sku','field-short-desc','field-desc','selling-price','buying-price'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el && data[id] !== undefined && el.value === '') el.value = data[id];
            });
        } catch(e) {}
    }

    function clearDraft() {
        try { localStorage.removeItem(LS_KEY); } catch(e) {}
    }

    /* ── Progress bar state ────────────────────────── */
    function updateStepper(n) {
        var bar = document.getElementById('gv-step-bar');
        if (bar) bar.style.width = WIDTHS[n] || '20%';

        var progress = bar ? bar.closest('[role="progressbar"]') : null;
        if (progress) progress.setAttribute('aria-valuenow', String(n));

        updateFooter(n);
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
    var SAVE_LABEL = <?= json_encode($editProduct ? 'Save Changes' : 'Save Product') ?>;
    function updateFooter(n) {
        /* Back button — hidden only on step 1 */
        var back = document.getElementById('gv-btn-back');
        if (back) back.classList.toggle('hidden', n === 1);

        /* Primary button morphs between Save & Continue and Save */
        var btn = document.getElementById('gv-btn-primary');
        if (!btn) return;
        if (n === TOTAL) {
            btn.type = 'submit';
            btn.setAttribute('form', 'product-form');
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
        el.focus();
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
            var skuEl = document.getElementById('field-sku');
            if (nameEl && !nameEl.value.trim()) {
                showError(nameEl, 'Product name is required.');
                ok = false;
            }
            if (skuEl && !skuEl.value.trim()) {
                showError(skuEl, 'SKU is required.');
                ok = false;
            }
            /* Category multi-select — custom select uses hidden inputs, not a native <select> */
            var catWrapper = stepEl.querySelector('[data-custom-select][data-name="category_ids[]"]');
            if (catWrapper) {
                var catTrigger = catWrapper.querySelector('button[aria-haspopup="listbox"]');
                var chosen = catWrapper.querySelectorAll('[data-select-option][data-selected="true"]').length > 0;
                if (!chosen) {
                    if (catTrigger) showError(catTrigger, 'Please select at least one category.');
                    ok = false;
                }
            }
        }
        if (n === 3) {
            var isEdit = !!document.querySelector('#product-form input[name="id"]');
            var fileInput = document.getElementById('product-images');
            var existingImages = stepEl.querySelectorAll('input[name="delete_images[]"]').length;
            var deletedImages = stepEl.querySelectorAll('input[name="delete_images[]"]:checked').length;
            var hasNewFiles = fileInput && fileInput.files && fileInput.files.length > 0;
            var hasRemaining = (existingImages - deletedImages) > 0;
            if ((!isEdit && !hasNewFiles) || (isEdit && !hasRemaining && !hasNewFiles)) {
                var fileTrigger = stepEl.querySelector('[data-file-input] label[for="product-images"]');
                if (fileTrigger) showError(fileTrigger, 'Upload at least one product image.');
                ok = false;
            }
        }
        if (n === 5) {
            var sp = document.getElementById('selling-price');
            var bp = document.getElementById('buying-price');
            if (sp && !sp.value.trim()) { showError(sp, 'Selling price is required.'); ok = false; }
            if (bp && !bp.value.trim()) { showError(bp, 'Cost price is required.'); ok = false; }
        }
        return ok;
    }

    /* ── Delegation ────────────────────────────────── */
    document.addEventListener('click', function (e) {
        /* Back */
        var backBtn = e.target.closest('#gv-btn-back');
        if (backBtn) { goTo(current - 1); return; }

        /* Save & Continue — validate first, then advance */
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

        /* Save Product — validate all steps, then submit */
        if (primBtn && primBtn.type === 'submit') {
            e.preventDefault();
            if (!validateAllSteps()) {
                toast('Please complete all required fields before saving.', 'error');
                return;
            }
            var form = document.getElementById('product-form');
            if (!form) return;
            clearDraft();
            if (form.requestSubmit) form.requestSubmit(primBtn);
            else form.submit();
            return;
        }
    });

    /* ── Profit ────────────────────────────────────── */
    function calcProfit() {
        var s = parseFloat(document.getElementById('selling-price')?.value) || 0;
        var b = parseFloat(document.getElementById('buying-price')?.value)  || 0;
        var p = s - b;
        var disp = document.getElementById('profit-display');
        if (!disp) return;
        disp.textContent = 'LKR ' + p.toFixed(2);
        disp.className   = 'mt-0.5 text-2xl font-black ' + (p >= 0 ? 'text-emerald-600' : 'text-rose-500');
    }
    document.getElementById('selling-price')?.addEventListener('input', calcProfit);
    document.getElementById('buying-price')?.addEventListener('input', calcProfit);
    calcProfit();

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

    /* ── Social links — plain URL rows ────────────── */
    var socialList  = document.getElementById('social-links-list');
    var noSocialMsg = document.getElementById('no-social-msg');

    function makeSocialRow(url) {
        var row = document.createElement('div');
        row.className = 'social-link-row flex gap-2';
        row.innerHTML =
            '<input type="url" name="social_url[]" value="' + (url || '') + '" placeholder="https://instagram.com/giftvibe or https://wa.me/94…" class="' + FC + '">' +
            '<button type="button" class="remove-social shrink-0 rounded-xl border border-rose-200 px-3 text-rose-500 hover:bg-rose-50 transition cursor-pointer">&times;</button>';
        row.querySelector('.remove-social').addEventListener('click', function () {
            row.remove();
            if (socialList && socialList.querySelectorAll('.social-link-row').length === 0 && noSocialMsg) {
                noSocialMsg.classList.remove('hidden');
            }
        });
        return row;
    }

    /* Bind remove on pre-rendered rows */
    socialList && socialList.querySelectorAll('.remove-social').forEach(function (btn) {
        btn.addEventListener('click', function () {
            btn.closest('.social-link-row').remove();
            if (socialList.querySelectorAll('.social-link-row').length === 0 && noSocialMsg) {
                noSocialMsg.classList.remove('hidden');
            }
        });
    });

    document.getElementById('add-social-link')?.addEventListener('click', function () {
        if (noSocialMsg) noSocialMsg.classList.add('hidden');
        socialList.appendChild(makeSocialRow(''));
    });

    /* ── Keyword tags ──────────────────────────────── */
    var hiddenKw  = document.getElementById('gv-keywords-hidden');
    var tagsCont  = document.getElementById('gv-tags-container');
    var tagInput  = document.getElementById('gv-tag-input');
    var tags      = [];

    if (hiddenKw && hiddenKw.value.trim()) {
        tags = hiddenKw.value.split(',').map(function (t) { return t.trim().replace(/^#+/, ''); }).filter(Boolean);
    }
    function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;'); }
    function renderTags() {
        if (!tagsCont) return;
        tagsCont.innerHTML = '';
        tags.forEach(function (tag, i) {
            var span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1 rounded-lg bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary';
            span.innerHTML = '#' + esc(tag) + '<button type="button" data-ti="' + i + '" class="ml-0.5 leading-none cursor-pointer hover:text-rose-600">&times;</button>';
            tagsCont.appendChild(span);
        });
        if (hiddenKw) hiddenKw.value = tags.join(', ');
    }
    tagsCont && tagsCont.addEventListener('click', function (e) {
        var b = e.target.closest('[data-ti]');
        if (b) { tags.splice(parseInt(b.getAttribute('data-ti'),10),1); renderTags(); }
    });
    tagInput && tagInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            var v = tagInput.value.trim().replace(/^#+/, '');
            if (v && !tags.includes(v)) { tags.push(v); renderTags(); }
            tagInput.value = '';
        }
    });

    /* Suggested tag buttons */
    document.querySelectorAll('[data-suggest-tag]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var v = btn.getAttribute('data-suggest-tag').replace(/^#+/, '');
            if (v && !tags.includes(v)) { tags.push(v); renderTags(); }
        });
    });

    renderTags();

    /* ── Final save guard ──────────────────────────── */
    document.getElementById('product-form')?.addEventListener('submit', function (e) {
        if (!validateAllSteps()) {
            e.preventDefault();
            toast('Please complete all required fields before saving.', 'error');
            return;
        }
        clearDraft();
    });

    /* ── Load draft + init ─────────────────────────── */
    loadDraft();
    goTo(1);
});
</script>
<?php
$stepperDrawerBody = (string) ob_get_clean();
$stepperDrawerId = 'product-drawer';
$stepperDrawerSteps = array_values($steps);
$stepperDrawerTitle = $editProduct ? 'Edit product' : 'Add product';
$stepperDrawerDescription = '';
$stepperDrawerPrefix = 'gv';
$stepperDrawerFormId = 'product-form';
$stepperDrawerSubmitLabel = $editProduct ? 'Save Changes' : 'Save Product';
$stepperDrawerTrigger = '<span class="hidden" aria-hidden="true"></span>';
$stepperDrawerSize = 'lg';
$stepperDrawerExternalNavigation = true;
$stepperDrawerShowHeader = true;
require BASE_PATH . '/resources/views/components/drawer/stepper-drawer.php';
?>
<script>
/* Give drawer title a stable id for JS to update */
(function () {
    var h3 = document.querySelector('#product-drawer aside h3');
    if (h3) h3.id = 'product-drawer-title';
    /* Footer: Back left, primary right */
    var footer = document.querySelector('#product-drawer aside > div:last-child');
    if (footer) {
        footer.classList.remove('justify-end');
        footer.classList.add('w-full', 'justify-between');
    }
})();
</script>
<?php if ($editProduct): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('[data-drawer-open="product-drawer"]')?.click();
});
</script>
<?php endif; ?>
