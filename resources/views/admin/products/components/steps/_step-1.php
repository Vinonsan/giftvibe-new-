    <div id="gv-step-1" data-step-panel="1" class="space-y-4">

        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">
                Product name <span class="text-primary">*</span>
            </span>
            <input id="field-name" required name="name"
                   value="<?= htmlspecialchars((string)($editProduct['name'] ?? '')) ?>"
                   placeholder="e.g. Rose Bouquet Premium"
                   class="<?= $fc ?>">
        </label>

        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Short description</span>
            <textarea id="field-short-desc" name="short_description" rows="2"
                      placeholder="Brief one-line summary shown in listings"
                      class="<?= $fc ?>"><?= htmlspecialchars((string)($editProduct['short_description'] ?? '')) ?></textarea>
        </label>

        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Full description</span>
            <textarea id="field-desc" name="description" rows="4"
                      placeholder="Contents, occasions, gifting details…"
                      class="<?= $fc ?>"><?= htmlspecialchars((string)($editProduct['description'] ?? '')) ?></textarea>
        </label>

        <?php
        $selectName       = 'category_ids[]';
        $selectId         = 'product-categories';
        $selectOptions    = [];
        foreach ($categories as $cat) $selectOptions[(string)$cat['id']] = $cat['name'];
        $selectValue       = array_map('strval', $editCategoryIds);
        $selectPlaceholder = 'Choose one or more categories';
        $selectLabel       = 'Categories';
        $selectHint        = '';
        $selectError       = '';
        $selectSize        = 'lg';
        $selectState       = 'default';
        $selectMultiple    = true;
        $selectRequired    = true;
        $selectDisabled    = false;
        $selectAttributes  = [];
        $selectClass       = '';
        require BASE_PATH . '/resources/views/components/base/select.php';
        ?>

        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">Search keywords <span class="ml-1 font-normal text-slate-400">— press Enter or , to add</span></span>
            <div id="gv-tags-box" class="flex min-h-[44px] cursor-text flex-wrap items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-2.5 transition focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/10" onclick="document.getElementById('gv-tag-input').focus()">
                <div id="gv-tags-container" class="contents"></div>
                <input type="text" id="gv-tag-input" placeholder="#birthday #flowers #surprise" autocomplete="off" class="min-w-[100px] flex-1 bg-transparent py-0.5 text-sm text-secondary outline-none">
            </div>
            <input type="hidden" name="search_keywords" id="gv-keywords-hidden" value="<?= htmlspecialchars((string)($editProduct['search_keywords'] ?? '')) ?>">
        </label>
    </div>
