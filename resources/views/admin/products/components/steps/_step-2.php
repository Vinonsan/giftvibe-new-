    <div id="gv-step-2" data-step-panel="2" class="space-y-4 hidden">
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold text-secondary">
                Search Keywords
                <span class="font-normal text-slate-400 ml-1">— press Enter or , to add</span>
            </span>
            <div id="gv-tags-box"
                 class="flex min-h-[44px] flex-wrap items-center gap-1.5 rounded-xl border border-slate-200 bg-white p-2.5 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/10 transition cursor-text"
                 onclick="document.getElementById('gv-tag-input').focus()">
                <div id="gv-tags-container" class="contents"></div>
                <input type="text" id="gv-tag-input"
                       placeholder="#birthday #flowers #surprise"
                       autocomplete="off"
                       class="flex-1 bg-transparent text-sm text-secondary outline-none min-w-[100px] py-0.5">
            </div>
            <input type="hidden" name="search_keywords" id="gv-keywords-hidden"
                   value="<?= htmlspecialchars((string)($editProduct['search_keywords'] ?? '')) ?>">
        </label>
    </div>
