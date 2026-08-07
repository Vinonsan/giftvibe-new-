<?php
declare(strict_types=1);

$renderInput = static function(string $name, string $label, string $value = '', string $type = 'text', bool $required = false, array $attributes = [], string $placeholder = ''): void {
    $inputType = $type;
    $inputName = $name;
    $inputId = $name;
    $inputValue = $value;
    $inputPlaceholder = $placeholder;
    $inputLabel = $label;
    $inputHint = '';
    $inputError = '';
    $inputSize = 'md';
    $inputState = 'default';
    $inputRequired = $required;
    $inputAutocomplete = '';
    $inputReadonly = false;
    $inputDisabled = false;
    $inputLeadingIcon = '';
    $inputPrefix = '';
    $inputTrailingIcon = '';
    $inputSuffix = '';
    $inputAttributes = $attributes;
    $inputClass = '';
    $inputWrapperClass = '';
    require BASE_PATH . '/resources/views/components/base/input.php';
};
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between rounded-2xl border border-primary/10 bg-white p-5 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-secondary">Financial Reports</h1>
            <p class="mt-1 text-sm text-slate-500">Track and calculate profit margins by comparing buying costs (cost prices) and selling prices.</p>
        </div>
        <button type="button" data-drawer-open="add-transaction-drawer" class="rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white hover:bg-secondary cursor-pointer shadow-md shadow-primary/10 transition-all flex items-center gap-1.5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span>Log Transaction</span>
        </button>
    </div>

    <?php if ($flash): ?>
        <span class="hidden" data-toast-message="<?= htmlspecialchars((string)$flash['message'], ENT_QUOTES) ?>" data-toast-type="<?= htmlspecialchars((string)$flash['type'], ENT_QUOTES) ?>"></span>
    <?php endif; ?>

    <!-- Filters Bar -->
    <div class="flex flex-wrap items-center gap-3">
        <div class="w-full max-w-[180px]">
            <?php
            $selectName = 'finance_year_filter';
            $selectId = 'finance-year-filter';
            $selectOptions = [
                '2026' => 'Year 2026',
                '2025' => 'Year 2025',
                '2024' => 'Year 2024'
            ];
            $selectValue = '';
            $selectPlaceholder = 'All Years';
            $selectLabel = '';
            $selectHint = '';
            $selectError = '';
            $selectSize = 'md';
            $selectState = 'default';
            $selectMultiple = false;
            $selectRequired = false;
            $selectDisabled = false;
            $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'finance-table', 'data-filter-key' => 'year'];
            $selectClass = '';
            require BASE_PATH . '/resources/views/components/base/select.php';
            ?>
        </div>
        <div class="w-full max-w-[180px]">
            <?php
            $selectName = 'finance_month_filter';
            $selectId = 'finance-month-filter';
            $selectOptions = [
                'january' => 'January',
                'february' => 'February',
                'march' => 'March',
                'april' => 'April',
                'may' => 'May',
                'june' => 'June',
                'july' => 'July',
                'august' => 'August',
                'september' => 'September',
                'october' => 'October',
                'november' => 'November',
                'december' => 'December'
            ];
            $selectValue = '';
            $selectPlaceholder = 'All Months';
            $selectLabel = '';
            $selectHint = '';
            $selectError = '';
            $selectSize = 'md';
            $selectState = 'default';
            $selectMultiple = false;
            $selectRequired = false;
            $selectDisabled = false;
            $selectAttributes = ['data-datatable-filter' => '', 'data-datatable-target' => 'finance-table', 'data-filter-key' => 'month'];
            $selectClass = '';
            require BASE_PATH . '/resources/views/components/base/select.php';
            ?>
        </div>
        <button type="button" data-datatable-reset data-datatable-target="finance-table" class="rounded-xl border border-primary/10 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-primary/5 hover:text-primary">Reset Filters</button>
    </div>

    <!-- Summary Widgets -->
    <div class="grid gap-5 sm:grid-cols-3">
        <!-- Revenue Card -->
        <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group hover:border-[#FF5A79]/30 transition-all">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-emerald-500/5 group-hover:scale-110 transition-transform"></div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Cash In (Income)</span>
                <h2 class="text-2xl font-black text-secondary mt-3">LKR <?= number_format($totalSales, 2) ?></h2>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                <span>Earned from sales & manual inputs</span>
                <span class="rounded-full bg-emerald-50 border border-emerald-100 px-2 py-0.5 text-[9px] font-bold text-emerald-700">Full Income</span>
            </div>
        </div>

        <!-- Buying Cost Card -->
        <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group hover:border-[#FF5A79]/30 transition-all">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-slate-500/5 group-hover:scale-110 transition-transform"></div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Expenses (Buying Cost)</span>
                <h2 class="text-2xl font-black text-secondary mt-3">LKR <?= number_format($totalCost, 2) ?></h2>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                <span>Production & manual expenses</span>
                <span class="rounded-full bg-slate-50 border border-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-600">Expenses</span>
            </div>
        </div>

        <!-- Profit Card -->
        <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group hover:border-[#FF5A79]/30 transition-all">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-[#FF5A79]/5 group-hover:scale-110 transition-transform"></div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Cash on Hand (Available Net Profit)</span>
                <h2 class="text-2xl font-black text-[#FF5A79] mt-3">LKR <?= number_format($totalProfit, 2) ?></h2>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                <span>Net money left in hand</span>
                <span class="rounded-full bg-[#FF5A79]/10 px-2 py-0.5 text-[9px] font-bold text-[#FF5A79]"><?= number_format($margin, 1) ?>% Margin</span>
            </div>
        </div>
    </div>

    <!-- Visual Monthly Trends (Mock Data UI) -->
    <div class="rounded-2xl border border-primary/10 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-bold text-secondary">6-Month Buying vs Selling Analysis</h3>
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Mock Trends Visualizer</span>
        </div>
        <div class="flex items-end gap-3 sm:gap-6 h-56 pt-6 border-b border-slate-100">
            <?php 
            $maxVal = max(array_column($monthlyReport, 'sales')) ?: 1;
            foreach ($monthlyReport as $data): 
                $saleHeight = ($data['sales'] / $maxVal) * 100;
                $costHeight = ($data['cost'] / $maxVal) * 100;
                $profitHeight = ($data['profit'] / $maxVal) * 100;
            ?>
                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                    <div class="w-full flex items-end gap-1.5 h-full justify-center">
                        <!-- Cost bar (buying) -->
                        <div style="height: <?= $costHeight ?>%" class="w-2.5 bg-slate-200/80 rounded-t-md transition-all hover:bg-slate-300" title="Cost: LKR <?= number_format($data['cost']) ?>"></div>
                        <!-- Profit bar -->
                        <div style="height: <?= $profitHeight ?>%" class="w-2.5 bg-[#FF5A79] rounded-t-md transition-all hover:opacity-90 shadow-sm shadow-rose-500/20" title="Profit: LKR <?= number_format($data['profit']) ?>"></div>
                        <!-- Sales bar (selling) -->
                        <div style="height: <?= $saleHeight ?>%" class="w-2.5 bg-slate-900 rounded-t-md transition-all hover:bg-slate-800" title="Sales: LKR <?= number_format($data['sales']) ?>"></div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 truncate"><?= htmlspecialchars($data['month']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-5 flex items-center justify-center gap-6 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-slate-200"></span> Buying Cost</span>
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-[#FF5A79]"></span> Net Profit</span>
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-slate-900"></span> Selling Price</span>
        </div>
    </div>

    <!-- Table breakdown -->
    <div class="space-y-4">
        <h3 class="text-sm font-bold text-secondary">Financial breakdown report</h3>
        <?php
        $rows = [];
        $monthNames = [
            '01' => 'january', '02' => 'february', '03' => 'march', '04' => 'april',
            '05' => 'may', '06' => 'june', '07' => 'july', '08' => 'august',
            '09' => 'september', '10' => 'october', '11' => 'november', '12' => 'december'
        ];

        foreach ($orderReports as $rep) {
            $pMargin = $rep['sales'] > 0 ? ($rep['profit'] / $rep['sales']) * 100 : 0;
            
            // Extract year and month slugs for hidden datatable matching
            $parts = explode('-', $rep['date']);
            $yr = $parts[0] ?? '';
            $mo = $monthNames[$parts[1] ?? ''] ?? '';

            $rows[] = [
                'order_number' => '<strong class="text-secondary">' . htmlspecialchars($rep['order_number']) . '</strong>',
                'customer_name' => '<strong>' . htmlspecialchars($rep['customer_name']) . '</strong>',
                'sales' => 'LKR ' . number_format($rep['sales'], 2),
                'cost' => 'LKR ' . number_format($rep['cost'], 2),
                'profit' => '<strong class="text-[#FF5A79]">LKR ' . number_format($rep['profit'], 2) . '</strong>',
                'margin' => '<span class="rounded-full bg-[#FF5A79]/10 px-2.5 py-0.5 text-xs font-bold text-[#FF5A79]">' . number_format($pMargin, 1) . '%</span>',
                'date' => '<span class="text-xs text-slate-400">' . htmlspecialchars($rep['date']) . '</span>',
                'year' => $yr,
                'month' => $mo
            ];
        }

        $tableId = 'finance-table';
        $tableRows = $rows;
        $tableColumns = [
            ['key' => 'order_number', 'label' => 'Reference #', 'html' => true],
            ['key' => 'customer_name', 'label' => 'Description / Source', 'html' => true],
            ['key' => 'sales', 'label' => 'Income Cash In', 'type' => 'number'],
            ['key' => 'cost', 'label' => 'Expenses Cash Out', 'type' => 'number'],
            ['key' => 'profit', 'label' => 'Net Profit', 'html' => true, 'type' => 'number'],
            ['key' => 'margin', 'label' => 'Profit Margin', 'html' => true, 'type' => 'number'],
            ['key' => 'date', 'label' => 'Date', 'html' => true, 'type' => 'date'],
            ['key' => 'year', 'label' => 'Year', 'class' => 'hidden'],
            ['key' => 'month', 'label' => 'Month', 'class' => 'hidden']
        ];
        $tablePerPage = 10;
        $tableZebra = false;
        $tableEmptyMessage = 'No transactions found to generate financial reports.';
        require BASE_PATH . '/resources/views/components/base/datatable.php';
        ?>
    </div>
</div>

<!-- Manual Transaction Log Drawer -->
<?php ob_start(); ?>
<form id="add-transaction-form" method="post" class="space-y-4">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="action" value="add_transaction">

    <div>
        <?php
        $selectName = 'type';
        $selectId = 'transaction-type-select';
        $selectOptions = [
            'income' => 'Cash In (Income)',
            'expense' => 'Cash Out (Expense)'
        ];
        $selectValue = 'income';
        $selectPlaceholder = 'Select Transaction Type';
        $selectLabel = 'Transaction Type';
        $selectRequired = true;
        require BASE_PATH . '/resources/views/components/base/select.php';
        ?>
    </div>

    <?php $renderInput('category', 'Category / Description', '', 'text', true, [], 'e.g. Hamper Box Materials, Delivery Fees'); ?>
    <?php $renderInput('amount', 'Transaction Amount (LKR)', '', 'number', true, ['step' => '0.01', 'min' => '0.01'], '0.00'); ?>
    <?php $renderInput('date', 'Transaction Date', date('Y-m-d'), 'date', true); ?>

    <label class="block space-y-1.5">
        <span class="text-xs font-semibold text-secondary">Extra Remarks / Notes</span>
        <textarea name="description" rows="3" class="w-full rounded-xl border border-primary/10 bg-white p-3 text-xs text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" placeholder="Optional reference notes..."></textarea>
    </label>

    <button type="submit" class="w-full rounded-xl bg-primary py-3 text-xs font-bold text-white hover:bg-secondary cursor-pointer transition">Log Transaction</button>
</form>
<?php
$drawerBody = ob_get_clean();
$drawerFooter = '<button data-drawer-close class="w-full rounded-xl border border-primary/10 py-3 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer transition">Close Panel</button>';
$drawerId = 'add-transaction-drawer';
$drawerSide = 'right';
$drawerSize = 'md';
$drawerTitle = 'Log Financial Transaction';
$drawerDescription = 'Manually record extra business income cash inflows or expense cash outflows.';
$drawerTrigger = '<span class="hidden"></span>';
$drawerStatic = false;
$drawerCloseOnEsc = true;
$drawerShowCloseButton = true;
$drawerOverlay = true;
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>
