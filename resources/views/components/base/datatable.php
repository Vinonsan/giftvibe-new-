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
 *   $tableTitle          string   Optional title shown above the table.
 *   $tableSearchable     bool     Show the search box (default: true).
 *   $tableSearchPlaceholder string Placeholder text for search.
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
 * @var string|null  $tableTitle
 * @var bool         $tableSearchable
 * @var string       $tableSearchPlaceholder
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
$tableTitle        = $tableTitle        ?? '';
$tableSearchable   = $tableSearchable   ?? true;
$tableSearchPlaceholder = $tableSearchPlaceholder ?? 'Search…';
$tableSortable     = $tableSortable     ?? true;
$tableDefaultSort  = $tableDefaultSort  ?? null;
$tablePaginated    = $tablePaginated    ?? true;
$tablePerPage      = max(1, (int) ($tablePerPage ?? 10));
$tableZebra        = $tableZebra        ?? true;
$tableEmptyMessage = $tableEmptyMessage ?? 'No records found.';
$tableFooter       = $tableFooter       ?? false;
$tableClass        = $tableClass        ?? '';
$tableAttributes   = $tableAttributes   ?? [];

$alignMap = ['left' => 'text-left', 'center' => 'text-center', 'right' => 'text-right'];

$tableAttr = '';
foreach ($tableAttributes as $attrName => $attrValue) {
    $tableAttr .= ' ' . $attrName . '="' . htmlspecialchars((string) $attrValue, ENT_QUOTES) . '"';
}

$defaultSort = $tableDefaultSort ?? [];
$defaultKey  = $defaultSort['key']  ?? '';
$defaultDir  = ($defaultSort['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
?>
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm <?= htmlspecialchars($tableClass) ?>"
     data-datatable
     data-id="<?= htmlspecialchars($tableId) ?>"
     data-per-page="<?= $tablePerPage ?>"
     data-sortable="<?= $tableSortable ? '1' : '0' ?>"
     data-default-key="<?= htmlspecialchars($defaultKey) ?>"
     data-default-dir="<?= $defaultDir ?>">

    <?php if ($tableTitle !== '' || $tableSearchable): ?>
        <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <?php if ($tableTitle !== ''): ?>
                <h3 class="text-base font-semibold text-secondary"><?= htmlspecialchars((string) $tableTitle) ?></h3>
            <?php endif; ?>

            <?php if ($tableSearchable): ?>
                <div class="relative w-full sm:w-64">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </span>
                    <input
                        type="search"
                        data-datatable-search
                        placeholder="<?= htmlspecialchars($tableSearchPlaceholder) ?>"
                        class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-secondary shadow-sm outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200"<?= $tableAttr ?>>
            <thead class="bg-slate-50">
                <tr>
                    <?php foreach ($tableColumns as $column): ?>
                        <?php
                        $colKey  = $column['key'] ?? '';
                        $label   = $column['label'] ?? '';
                        $align   = $alignMap[$column['align'] ?? 'left'] ?? 'text-left';
                        $width   = $column['width'] ?? '';
                        $sortable = $tableSortable && ($column['sortable'] ?? true) && $colKey !== '';
                        $sortType = $column['type'] ?? 'string';
                        $thClass = trim('px-4 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 ' . $align . ' ' . $width);
                        ?>
                        <th scope="col" class="<?= $thClass ?>" <?= $sortable ? 'data-sort-key="' . htmlspecialchars($colKey) . '" data-sort-type="' . htmlspecialchars($sortType) . '"' : '' ?>>
                            <?php if ($sortable): ?>
                                <button type="button" class="group inline-flex items-center gap-1.5 uppercase tracking-wider hover:text-secondary" data-sort-trigger>
                                    <?= htmlspecialchars((string) $label) ?>
                                    <span class="flex flex-col leading-none" aria-hidden="true">
                                        <svg class="h-2.5 w-2.5 text-slate-400" data-sort-indicator-asc viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a.75.75 0 01.53.22l4 4a.75.75 0 11-1.06 1.06L10 6.81 6.53 10.28a.75.75 0 01-1.06-1.06l4-4A.75.75 0 0110 5z" clip-rule="evenodd"/></svg>
                                        <svg class="h-2.5 w-2.5 text-slate-400" data-sort-indicator-desc viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 15a.75.75 0 01-.53-.22l-4-4a.75.75 0 111.06-1.06L10 13.19l3.47-3.47a.75.75 0 011.06 1.06l-4 4A.75.75 0 0110 15z" clip-rule="evenodd"/></svg>
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
                    <tr data-row class="transition hover:bg-slate-50 <?= $tableZebra ? 'odd:bg-slate-50/60' : '' ?>">
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
                            <td class="px-4 py-3 text-sm <?= $align ?>" <?= $colKey !== '' ? 'data-key="' . htmlspecialchars($colKey) . '"' : '' ?>>
                                <?= $escape ? htmlspecialchars($cellValue) : $cellValue ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <?php if ($tableFooter): ?>
                <tfoot class="border-t border-slate-200 bg-slate-50">
                    <tr>
                        <td colspan="<?= count($tableColumns) ?>" class="px-4 py-3 text-sm font-semibold text-secondary">
                            <?= count($tableRows) ?> record<?= count($tableRows) === 1 ? '' : 's' ?>
                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>

    <div data-table-empty class="hidden px-4 py-12 text-center">
        <p class="text-sm text-slate-500"><?= htmlspecialchars($tableEmptyMessage) ?></p>
    </div>

    <div class="flex flex-col items-center justify-between gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row">
        <p data-table-summary class="text-sm text-slate-500"></p>
        <nav data-table-pagination class="flex flex-wrap items-center justify-end gap-1" aria-label="Pagination"></nav>
    </div>
</div>

<script>
(function () {
    if (window.GiftVibeUI && window.GiftVibeUI.datatable) { return; }
    window.GiftVibeUI = window.GiftVibeUI || {};

    function initTable(root) {
        var tbody = root.querySelector('tbody');
        var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr[data-row]'));
        var searchInput = root.querySelector('[data-datatable-search]');
        var emptyEl = root.querySelector('[data-table-empty]');
        var summaryEl = root.querySelector('[data-table-summary]');
        var paginationEl = root.querySelector('[data-table-pagination]');
        var perPage = parseInt(root.getAttribute('data-per-page'), 10) || 10;
        var sortable = root.getAttribute('data-sortable') === '1';

        var state = {
            query: '',
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
                var asc = th.querySelector('[data-sort-indicator-asc]');
                var desc = th.querySelector('[data-sort-indicator-desc]');
                var active = th.getAttribute('data-sort-key') === state.sortKey;
                if (asc) { asc.classList.remove('text-primary'); asc.classList.toggle('opacity-0', !(active && state.sortDir === 'asc')); }
                if (desc) { desc.classList.remove('text-primary'); desc.classList.toggle('opacity-0', !(active && state.sortDir === 'desc')); }
                if (active) {
                    if (state.sortDir === 'asc' && asc) { asc.classList.add('text-primary'); }
                    if (state.sortDir === 'desc' && desc) { desc.classList.add('text-primary'); }
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

            /* Reorder the DOM so the visible rows appear in sorted order. */
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
                    ? 'No results'
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
            btn.className = 'h-8 min-w-8 rounded-lg px-2 text-sm transition ' +
                (disabled ? 'cursor-not-allowed text-slate-300' :
                 (active ? 'bg-primary font-semibold text-white' : 'text-slate-600 hover:bg-slate-100'));
            if (disabled) { btn.disabled = true; }
            btn.addEventListener('click', function () { state.page = page; render(); });
            return btn;
        }

        function renderPagination(pages) {
            paginationEl.innerHTML = '';
            if (pages <= 1) { return; }
            paginationEl.appendChild(pageButton('\u2039', state.page - 1, false, state.page === 1));
            pageList(state.page, pages).forEach(function (p) {
                if (p === '…') {
                    var span = document.createElement('span');
                    span.className = 'px-1 text-sm text-slate-400';
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
