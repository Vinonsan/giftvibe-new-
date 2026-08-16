<?php
declare(strict_types=1);
?>
<div class="space-y-5">

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-secondary">Financial Reports</h1>
            <p class="mt-1 text-sm text-slate-500">Order cash-in and externally purchased product cash-out transactions.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <?php require __DIR__ . '/components/_filter.php'; ?>
    </div>

    <?php require __DIR__ . '/components/_summary.php'; ?>

    <?php require __DIR__ . '/components/_chart.php'; ?>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4"><h2 class="text-base font-bold text-secondary">Cash transactions</h2><p class="mt-1 text-xs text-slate-500">Orders add cash; externally purchased products deduct cash.</p></div>
        <?php require __DIR__ . '/components/_datatable.php'; ?>
    </div>

</div>

<?php require __DIR__ . '/components/_drawer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var table = document.querySelector('[data-datatable][data-id="finance-table"]');
    if (!table) return;

    var incomeEl = document.getElementById('finance-summary-income');
    var expenseEl = document.getElementById('finance-summary-expense');
    var profitEl = document.getElementById('finance-summary-profit');
    var marginEl = document.getElementById('finance-summary-margin');
    var countEl = document.getElementById('finance-summary-count');

    function money(n) {
        return 'LKR ' + Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateSummary(event) {
        var rows = (event && event.detail && event.detail.filtered) ? event.detail.filtered : [];
        var sales = 0;
        var cost = 0;
        var profit = 0;

        rows.forEach(function (row) {
            sales += parseFloat(row.getAttribute('data-sales') || '0') || 0;
            cost += parseFloat(row.getAttribute('data-cost') || '0') || 0;
            profit += parseFloat(row.getAttribute('data-profit') || '0') || 0;
        });

        var margin = sales > 0 ? (profit / sales) * 100 : 0;
        if (incomeEl) incomeEl.textContent = money(sales);
        if (expenseEl) expenseEl.textContent = money(cost);
        if (profitEl) profitEl.textContent = money(profit);
        if (marginEl) marginEl.textContent = margin.toFixed(1) + '% margin';
        if (countEl) countEl.textContent = String(rows.filter(function(row){ return row.getAttribute('data-kind') === 'order'; }).length);
    }

    table.addEventListener('datatable:render', updateSummary);
    updateSummary({ detail: { filtered: Array.prototype.slice.call(table.querySelectorAll('tbody tr[data-row]')) } });
});
</script>
