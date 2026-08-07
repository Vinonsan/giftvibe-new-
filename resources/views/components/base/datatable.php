<?php

declare(strict_types=1);

/**
 * DataTable component — client-side search, column sorting and pagination.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/datatable.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $tableId             string   Unique id for the table instance.
 *   $tableColumns        array    Column definitions. Each column:
 *                                   ['key'       => 'name',
 *                                    'label'     => 'Name',
 *                                    'sortable'  => true,      // default true (when sort enabled)
 *                                    'type'      => 'string',  // 'string' | 'number' | 'date'
 *                                    'align'     => 'left',    // left | center | right
 *                                    'width'     => 'w-32',
 *                                    'escape'    => true,      // escape cell output (default true)
 *                                    'html'      => false,     // treat value as raw HTML
 *                                    'format'    => fn($value, $row) => string,
 *                                    'render'    => fn($row) => string]   // fully custom cell
 *                                   Columns without a 'key' (e.g. an actions column)
 *                                   are read from $row['_actions'] or a 'render' callback.
 *   $tableRows           array    Array of rows. Each row: ['name' => ..., 'price' => ...,
 *                                   '_actions' => '<button>...</button>', ...].
 *   $tableSortable       bool     Allow column sorting (default: true).
 *   $tableDefaultSort    array    ['key' => 'name', 'dir' => 'asc'] initial sort.
 *   $tablePaginated      bool     Enable pagination (default: true).
 *   $tablePerPage        int      Rows per page (default: 10).
 *   $tableZebra          bool     Zebra striped rows (default: true).
 *   $tableEmptyMessage   string   Message shown when there are no rows.
 *   $tableFooter         bool     Show a totals footer row.
 *   $tableClass          string   Extra classes on the outer wrapper.
 *   $tableAttributes     array    Extra attributes on the <table>.
 *
 * -----------------------------------------------------------------------------
 * Example:
 * -----------------------------------------------------------------------------
 *   $tableId = 'products';
 *   $tableColumns = [
 *       ['key' => 'name',  'label' => 'Product', 'sortable' => true],
 *       ['key' => 'price', 'label' => 'Price', 'type' => 'number', 'align' => 'right',
 *        'format' => fn ($v) => '$' . number_format((float) $v, 2)],
 *       ['label' => '', 'align' => 'right', 'width' => 'w-28'],
 *   ];
 *   $tableRows = [
 *       ['name' => 'Teddy Bear', 'price' => 1500, '_actions' => '<a href="#">Edit</a>'],
 *   ];
 *   require 'datatable.php';
 *
 * @var string       $tableId
 * @var array        $tableColumns
 * @var array        $tableRows
 * @var bool         $tableSortable
 * @var array|null   $tableDefaultSort
 * @var bool         $tablePaginated
 * @var int          $tablePerPage
 * @var bool         $tableZebra
 * @var string       $tableEmptyMessage
 * @var bool         $tableFooter
 * @var string|null  $tableClass
 * @var array        $tableAttributes
 */

$tableId           = $tableId           ?? 'table-' . uniqid();
$tableColumns      = $tableColumns      ?? [];
$tableRows         = $tableRows         ?? [];
$tableSortable     = $tableSortable     ?? true;
$tableDefaultSort  = $tableDefaultSort  ?? null;
$tablePaginated    = $tablePaginated    ?? true;
$tablePerPage      = max(1, (int) ($tablePerPage ?? 10));
$tableZebra        = $tableZebra        ?? true;
$tableEmptyMessage = $tableEmptyMessage ?? 'No records found.';
$tableFooter       = $tableFooter       ?? false;
$tableClass        = $tableClass        ?? '';
$tableAttributes   = $tableAttributes   ?? [];
$tableEmbedded     = $tableEmbedded     ?? false;

$alignMap = ['left' => 'text-left', 'center' => 'text-center', 'right' => 'text-right'];

$tableAttr = '';
foreach ($tableAttributes as $attrName => $attrValue) {
    $tableAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$defaultSort = $tableDefaultSort ?? [];
$defaultKey  = $defaultSort['key']  ?? '';
$defaultDir  = ($defaultSort['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
?>
<div class="overflow-hidden bg-white <?= $tableEmbedded ? '' : 'rounded-2xl border border-slate-200 shadow-sm' ?> <?= htmlspecialchars($tableClass) ?>"
     data-datatable
     data-id="<?= htmlspecialchars($tableId) ?>"
     data-per-page="<?= $tablePerPage ?>"
     data-sortable="<?= $tableSortable ? '1' : '0' ?>"
     data-datatable
     data-id="<?= htmlspecialchars($tableId) ?>"
     data-per-page="<?= $tablePerPage ?>"
     data-sortable="<?= $tableSortable ? '1' : '0' ?>"
     data-default-key="<?= htmlspecialchars($defaultKey) ?>"
     data-default-dir="<?= $defaultDir ?>">

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100"<?= $tableAttr ?>>
            <thead class="bg-primary/5">
                <tr>
                    <?php foreach ($tableColumns as $column): ?>
                        <?php
                        $colKey  = $column['key'] ?? '';
                        $label   = $column['label'] ?? '';
                        $align   = $alignMap[$column['align'] ?? 'left'] ?? 'text-left';
                        $width   = $column['width'] ?? '';
                        $sortable = $tableSortable && ($column['sortable'] ?? true) && $colKey !== '';
                        $sortType = $column['type'] ?? 'string';
                        $thClass = trim('px-6 py-4 text-xs font-bold uppercase tracking-wider text-primary/60 ' . $align . ' ' . $width . ' ' . ($column['class'] ?? ''));
                        ?>
                        <th scope="col" class="<?= $thClass ?>" <?= $sortable ? 'data-sort-key="' . htmlspecialchars($colKey) . '" data-sort-type="' . htmlspecialchars($sortType) . '"' : '' ?>>
                            <?php if ($sortable): ?>
                                <button type="button" class="group inline-flex items-center gap-1.5 uppercase tracking-wider text-primary/60 hover:text-primary cursor-pointer focus:outline-none" data-sort-trigger>
                                    <span><?= htmlspecialchars((string) $label) ?></span>
                                    <span class="flex items-center" aria-hidden="true">
                                        <!-- Single Rotating Arrow for Sort State -->
                                        <svg class="h-3.5 w-3.5 text-slate-300 transition-all duration-200" data-sort-indicator viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </button>
                            <?php else: ?>
                                <?= htmlspecialchars((string) $label) ?>
                            <?php endif; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach ($tableRows as $row): ?>
                    <tr class="group hover:bg-slate-50/50 transition-colors" data-row>
                        <?php foreach ($tableColumns as $column): ?>
                            <?php
                            $colKey = $column['key'] ?? '';
                            $align  = $alignMap[$column['align'] ?? 'left'] ?? 'text-left';

                            $cellValue = '';
                            if (isset($column['render']) && is_callable($column['render'])) {
                                $cellValue = (string) $column['render']($row);
                                $escape = false;

                            } elseif (isset($column['format']) && is_callable($column['format'])) {
                                $cellValue = (string) $column['format']($row[$colKey] ?? '', $row);
                                $escape = !($column['html'] ?? false);
                            } elseif ($colKey !== '') {
                                $cellValue = (string) ($row[$colKey] ?? '');
                                $escape = !($column['html'] ?? false);
                            } else {
                                /* Key-less column (e.g. actions) → read from '_actions'. */
                                $cellValue = (string) ($row['_actions'] ?? '');
                                $escape = true;
                            }
                            ?>
                            <td class="px-6 py-4 text-sm text-slate-600 group-hover:text-secondary <?= $align ?> <?= htmlspecialchars($column['class'] ?? '') ?>" <?= $colKey !== '' ? 'data-key="' . htmlspecialchars($colKey) . '"' : '' ?>>
                                <?= $escape ? htmlspecialchars($cellValue) : $cellValue ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <?php if ($tableFooter): ?>
                <tfoot class="border-t border-slate-100 bg-slate-50/50">
                    <tr>
                        <td colspan="<?= count($tableColumns) ?>" class="px-6 py-4 text-sm font-semibold text-secondary">
                            <?= count($tableRows) ?> record<?= count($tableRows) === 1 ? '' : 's' ?>
                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>

    <div data-table-empty class="hidden px-6 py-16 text-center">
        <p class="text-sm text-slate-400"><?= htmlspecialchars($tableEmptyMessage) ?></p>
    </div>

    <?php if ($tablePaginated): ?>
        <div data-table-pagination-wrap data-datatable-target="<?= htmlspecialchars($tableId) ?>" class="flex flex-col gap-4 border-t border-primary/10 bg-slate-50/50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <label for="<?= htmlspecialchars($tableId) ?>-per-page" class="text-xs font-semibold text-slate-500">Rows per page</label>
                <select id="<?= htmlspecialchars($tableId) ?>-per-page" data-table-perpage data-datatable-target="<?= htmlspecialchars($tableId) ?>" class="rounded-lg border border-primary/10 bg-white px-2.5 py-1.5 text-xs font-bold text-secondary outline-none focus:border-primary">
                    <option value="5" <?= $tablePerPage === 5 ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= $tablePerPage === 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $tablePerPage === 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $tablePerPage === 50 ? 'selected' : '' ?>>50</option>
                </select>
            </div>
            <p data-table-summary data-datatable-target="<?= htmlspecialchars($tableId) ?>" class="text-xs font-semibold text-slate-500"></p>
            <nav data-table-pagination data-datatable-target="<?= htmlspecialchars($tableId) ?>" class="flex flex-wrap items-center justify-end gap-1" aria-label="Table pagination"></nav>
        </div>
    <?php endif; ?>

</div>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.datatable) { return; }
    window.GiftVibeUI = window.GiftVibeUI || {};

    function initTable(root) {
        var tbody = root.querySelector('tbody');
        var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr[data-row]'));
        var tableId = root.getAttribute('data-id');
        var searchInput = document.querySelector('[data-datatable-search][data-datatable-target="' + tableId + '"]');
        var filterInputs = document.querySelectorAll('[data-datatable-filter][data-datatable-target="' + tableId + '"]');
        var resetButton = document.querySelector('[data-datatable-reset][data-datatable-target="' + tableId + '"]');
        var emptyEl = root.querySelector('[data-table-empty]');
        var summaryEl = document.querySelector('[data-table-summary][data-datatable-target="' + tableId + '"]');
        var paginationEl = document.querySelector('[data-table-pagination][data-datatable-target="' + tableId + '"]');
        var paginationWrap = document.querySelector('[data-table-pagination-wrap][data-datatable-target="' + tableId + '"]');
        var perPageSelect = document.querySelector('[data-table-perpage][data-datatable-target="' + tableId + '"]');
        var perPage = parseInt(root.getAttribute('data-per-page'), 10) || 10;
        var sortable = root.getAttribute('data-sortable') === '1';

        var initialFilters = {};
        filterInputs.forEach(function (input) {
            var val = input.value;
            if (val) {
                initialFilters[input.getAttribute('data-filter-key')] = val.toLowerCase();
            }
        });

        var state = {
            query: '',
            filters: initialFilters,
            sortKey: root.getAttribute('data-default-key') || '',
            sortDir: root.getAttribute('data-default-dir') || 'asc',
            page: 1
        };

        function sortValue(key, type, row) {
            var cell = row.querySelector('td[data-key="' + key + '"]');
            var text = cell ? cell.textContent.trim() : '';
            if (type === 'number') {
                var num = parseFloat(text.replace(/[^0-9.\-]/g, ''));
                return isNaN(num) ? -Infinity : num;
            }
            if (type === 'date') {
                var d = new Date(text);
                return isNaN(d.getTime()) ? -Infinity : d.getTime();
            }
            return text.toLowerCase();
        }

        function filtered() {
            var list = rows;
            if (state.query) {
                var q = state.query.toLowerCase();
                list = rows.filter(function (row) { return row.textContent.toLowerCase().indexOf(q) !== -1; });
            }
            Object.keys(state.filters).forEach(function (key) {
                var value = state.filters[key];
                if (!value) return;
                list = list.filter(function (row) {
                    var cell = row.querySelector('td[data-key="' + key + '"]');
                    if (!cell) return false;
                    var text = cell.textContent.trim().toLowerCase();
                    var matchEl = cell.querySelector('[data-filter-match]');
                    if (matchEl) {
                        var matches = matchEl.getAttribute('data-filter-match').toLowerCase().split(',');
                        return matches.indexOf(value) !== -1;
                    }
                    if (key === 'year' || key === 'month' || key === 'date') {
                        return text.indexOf(value) !== -1;
                    }
                    return text === value;
                });
            });
            if (state.sortKey) {
                var th = root.querySelector('th[data-sort-key="' + state.sortKey + '"]');
                var type = th ? th.getAttribute('data-sort-type') : 'string';
                var dir = state.sortDir === 'desc' ? -1 : 1;
                list = list.slice().sort(function (a, b) {
                    var av = sortValue(state.sortKey, type, a);
                    var bv = sortValue(state.sortKey, type, b);
                    if (av === bv) { return 0; }
                    return av < bv ? -dir : dir;
                });
            }
            return list;
        }

        function renderSortIndicators() {
            root.querySelectorAll('th[data-sort-key]').forEach(function (th) {
                var indicator = th.querySelector('[data-sort-indicator]');
                var active = th.getAttribute('data-sort-key') === state.sortKey;
                if (indicator) {
                    if (active) {
                        indicator.classList.add('text-primary');
                        indicator.classList.remove('text-slate-300');
                        if (state.sortDir === 'desc') {
                            indicator.classList.add('rotate-180');
                        } else {
                            indicator.classList.remove('rotate-180');
                        }
                    } else {
                        indicator.classList.remove('text-primary', 'rotate-180');
                        indicator.classList.add('text-slate-300');
                    }
                }
            });
        }

        function render() {
            var list = filtered();
            var total = list.length;
            var pages = Math.max(1, Math.ceil(total / perPage));
            state.page = Math.min(Math.max(1, state.page), pages);

            var start = (state.page - 1) * perPage;
            var pageRows = list.slice(start, start + perPage);

            list.forEach(function (row) { tbody.appendChild(row); });

            rows.forEach(function (row) { row.classList.add('hidden'); });
            pageRows.forEach(function (row) { row.classList.remove('hidden'); });

            if (total === 0) {
                emptyEl.classList.remove('hidden');
            } else {
                emptyEl.classList.add('hidden');
            }

            if (summaryEl) {
                summaryEl.textContent = total === 0
                    ? 'Showing 0 of 0'
                    : 'Showing ' + (start + 1) + '\u2013' + Math.min(start + perPage, total) + ' of ' + total;
            }

            renderPagination(pages);
            renderSortIndicators();
        }

        function pageList(current, pages) {
            var out = [];
            if (pages <= 7) {
                for (var i = 1; i <= pages; i++) { out.push(i); }
                return out;
            }
            out.push(1);
            var start = Math.max(2, current - 1);
            var end = Math.min(pages - 1, current + 1);
            if (start > 2) { out.push('…'); }
            for (var j = start; j <= end; j++) { out.push(j); }
            if (end < pages - 1) { out.push('…'); }
            out.push(pages);
            return out;
        }

        function pageButton(label, page, active, disabled) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = label;
            btn.className = 'h-9 min-w-9 rounded-xl px-2.5 text-sm transition-all duration-200 flex items-center justify-center cursor-pointer font-bold ' +
                (disabled ? 'cursor-not-allowed text-slate-300 bg-transparent' :
                 (active ? 'bg-primary/10 text-primary' : 'text-slate-500 bg-transparent hover:bg-slate-100 hover:text-secondary'));
            if (disabled) { btn.disabled = true; }
            btn.addEventListener('click', function () { state.page = page; render(); });
            return btn;
        }

        function renderPagination(pages) {
            if (!paginationEl || !paginationWrap) return;
            paginationEl.innerHTML = '';
            paginationWrap.classList.remove('hidden');
            
            paginationEl.appendChild(pageButton('\u2039', state.page - 1, false, state.page === 1));
            pageList(state.page, pages).forEach(function (p) {
                if (p === '…') {
                    var span = document.createElement('span');
                    span.className = 'px-1.5 text-sm text-slate-400 font-semibold';
                    span.textContent = '…';
                    paginationEl.appendChild(span);
                } else {
                    paginationEl.appendChild(pageButton(String(p), p, p === state.page, false));
                }
            });
            paginationEl.appendChild(pageButton('\u203a', state.page + 1, false, state.page === pages));
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                state.query = searchInput.value;
                state.page = 1;
                render();
            });
        }

        filterInputs.forEach(function (input) {
            var customSelect = input.closest('[data-custom-select]');
            if (customSelect) {
                customSelect.addEventListener('select:change', function (e) {
                    var val = e.detail.values[0] || '';
                    state.filters[input.getAttribute('data-filter-key')] = val.toLowerCase();
                    state.page = 1;
                    render();
                });
            } else {
                input.addEventListener('change', function () {
                    state.filters[input.getAttribute('data-filter-key')] = input.value.toLowerCase();
                    state.page = 1;
                    render();
                });
            }
        });

        if (perPageSelect) {
            perPageSelect.addEventListener('change', function () {
                perPage = parseInt(perPageSelect.value, 10) || perPage;
                state.page = 1;
                render();
            });
        }

        if (resetButton) {
            resetButton.addEventListener('click', function () {
                state.query = '';
                state.filters = {};
                state.page = 1;
                if (searchInput) searchInput.value = '';
                filterInputs.forEach(function (input) { input.value = ''; });
                render();
            });
        }

        if (sortable) {
            root.querySelectorAll('[data-sort-trigger]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var th = btn.closest('th');
                    var key = th.getAttribute('data-sort-key');
                    if (state.sortKey === key) {
                        state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        state.sortKey = key;
                        state.sortDir = 'asc';
                    }
                    state.page = 1;
                    render();
                });
            });
        }

        render();
    }

    function initAll() {
        document.querySelectorAll('[data-datatable]').forEach(initTable);
    }

    document.addEventListener('DOMContentLoaded', initAll);
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initAll();
    }

    window.GiftVibeUI.datatable = true;
})();
</script>
