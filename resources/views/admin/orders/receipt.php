<?php
declare(strict_types=1);
/**
 * Ultra-modern print-ready invoice receipt template.
 *
 * @var array $order
 * @var array $items
 * @var array $payment
 * @var array $settings
 */
$receiptNo = (string) ($order['order_number'] ?? ('INV-' . $order['id']));
$logo = $settings['site_logo'] ?? '/assets/images/logo.svg';
$logo = str_starts_with((string) $logo, 'http') ? (string) $logo : app_url((string) $logo);
$siteName = 'GiftVibeLK';
$paymentStatus = (string) ($order['payment_status'] ?? $payment['status'] ?? 'pending');
$paymentMethod = ucfirst(str_replace('_', ' ', (string) ($payment['method'] ?? 'cod')));
$paidAmount = (float) ($payment['amount'] ?? 0);
$balanceDue = max(0, (float) $order['grand_total'] - $paidAmount);
$statusClass = $paymentStatus === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= htmlspecialchars($receiptNo) ?></title>
    <script src="<?= htmlspecialchars(app_url('/assets/js/tailwindcss-browser.js')) ?>"></script>
    <script src="<?= htmlspecialchars(app_url('/assets/js/html2pdf.bundle.min.js')) ?>"></script>
    <style>
        :root { --gv-primary: #2563eb; --gv-accent: #f97316; }
        .gv-text-primary { color: var(--gv-primary); }
        .gv-text-accent { color: var(--gv-accent); }
        .gv-bg-primary { background-color: var(--gv-primary); }
        .gv-border-soft { border-color: rgba(37, 99, 235, .14); }
        .gv-invoice-shadow { box-shadow: 0 24px 64px rgba(37, 99, 235, .10); }
        .gv-download-shadow { box-shadow: 0 8px 20px rgba(37, 99, 235, .20); }
        .gv-download:hover { background-color: var(--gv-accent); }
        @media print {
            body { background: white; color: black; padding: 0; }
            .no-print { display: none; }
            .print-card { border: none !important; box-shadow: none !important; padding: 0 !important; max-width: none !important; }
            @page { size: A4; margin: 12mm; }
        }
    </style>
</head>
<body class="bg-slate-50/30 font-sans text-slate-800 antialiased p-4 sm:p-8">

    <!-- Top floating action bar (Hidden on print) -->
    <div class="no-print mx-auto max-w-4xl mb-6 flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Invoice <?= htmlspecialchars($receiptNo) ?></span>
        <div class="flex gap-2">
            <button id="download-invoice-btn" onclick="downloadInvoice()" class="gv-bg-primary gv-download gv-download-shadow rounded-xl px-4 py-2.5 text-xs font-bold text-white transition-all cursor-pointer flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>
                <span>Download PDF</span>
            </button>
            <button onclick="window.print()" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all cursor-pointer flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-10.56 0a2.44 2.44 0 0 1-1.956-2.4L4.5 5.25h15l-.204 6.171a2.44 2.44 0 0 1-1.956 2.4m-10.56 0h10.56M12 3v3.75m0 0a1.5 1.5 0 0 1-3 0h6a1.5 1.5 0 0 1-3 0Z"/></svg>
                <span>Print</span>
            </button>
            <button onclick="window.close()" class="rounded-xl border border-slate-200 bg-white px-4.5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                Close Window
            </button>
        </div>
    </div>

    <!-- Printable Receipt Card -->
    <div id="invoice-document" class="gv-border-soft gv-invoice-shadow mx-auto max-w-4xl bg-white rounded-[2rem] border p-7 sm:p-10 relative print-card">
        
        <!-- Brand Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-6 mb-6">
            <div class="flex items-center gap-2.5">
                <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($siteName) ?> Logo" class="h-14 w-14 rounded-2xl bg-white object-contain ring-1 ring-slate-200">
                <div>
                    <h1 class="gv-text-primary text-xl font-black"><?= htmlspecialchars($siteName) ?></h1>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Official invoice</span>
                </div>
            </div>
            <div class="text-right text-xs">
                <span class="gv-text-accent text-sm font-black block"><?= htmlspecialchars($receiptNo) ?></span>
                <span class="text-[10px] text-slate-400 block mt-0.5"><?= htmlspecialchars(substr($order['created_at'], 0, 10)) ?></span>
            </div>
        </div>

        <!-- Customer & Order details -->
        <div class="grid grid-cols-2 gap-6 text-xs mb-6 bg-slate-50/50 rounded-2xl p-4.5 border border-slate-100/50">
            <div>
                <p class="gv-text-primary font-bold uppercase tracking-wider mb-1.5 text-[9px]">Billing To</p>
                <p class="font-bold text-slate-800"><?= htmlspecialchars($order['customer_name']) ?></p>
                <p class="text-slate-500 mt-0.5"><?= htmlspecialchars($order['customer_phone']) ?></p>
            </div>
            <div>
                <p class="gv-text-accent font-bold uppercase tracking-wider mb-1.5 text-[9px]">Shipping To</p>
                <p class="font-bold text-slate-800"><?= htmlspecialchars($order['recipient_name']) ?></p>
                <p class="text-slate-500 mt-0.5"><?= htmlspecialchars($order['recipient_phone']) ?></p>
                <p class="text-slate-500 leading-normal"><?= htmlspecialchars(implode(', ', array_filter([$order['delivery_address_line_1'] ?? '', $order['delivery_address_line_2'] ?? '', $order['delivery_city'] ?? '', $order['delivery_district'] ?? '']))) ?></p>
            </div>
        </div>

        <!-- Line items table -->
        <div class="mb-6">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold text-[9px] uppercase tracking-wider">
                        <th class="pb-2.5">Item</th>
                        <th class="pb-2.5 text-center w-12">Qty</th>
                        <th class="pb-2.5 text-right w-24">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($items)): ?>
                        <tr class="text-slate-700 font-medium">
                            <td class="py-3">Custom Gift Hampers / Packages</td>
                            <td class="py-3 text-center">1</td>
                            <td class="py-3 text-right">LKR <?= number_format((float) $order['grand_total'], 2) ?></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr class="text-slate-700 font-medium">
                                <td class="py-3">
                                    <span class="inline-flex items-center gap-2">
                                        <?php if (!empty($item['image_path'])): ?><img src="<?= htmlspecialchars(app_url((string) $item['image_path'])) ?>" alt="" class="h-10 w-12 shrink-0 rounded object-cover"><?php endif; ?>
                                        <span class="font-bold text-slate-800"><?= htmlspecialchars($item['product_name']) ?></span>
                                    </span>
                                    <?php if ($item['sku']): ?>
                                        <small class="block text-[9px] text-slate-400 font-normal mt-0.5">SKU: <?= htmlspecialchars($item['sku']) ?></small>
                                    <?php endif; ?>
                                    <?php $options = json_decode((string) ($item['custom_options_json'] ?? ''), true) ?: []; if (!empty($options['colour'])): ?>
                                        <small class="gv-text-accent block text-[9px] font-bold mt-0.5">Colour: <?= htmlspecialchars((string) $options['colour']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-center text-slate-500"><?= (int) $item['quantity'] ?></td>
                                <td class="py-3 text-right text-slate-900 font-bold">LKR <?= number_format((float) $item['total_price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals & Payment Summary -->
        <div class="border-t border-slate-100 pt-5 flex justify-between items-center text-xs">
            <div class="space-y-1">
                <span class="inline-flex rounded-full px-3 py-1 font-bold text-[10px] uppercase <?= $statusClass ?>"><?= htmlspecialchars($paymentStatus) ?></span>
                <p class="text-[10px] text-slate-500"><?= htmlspecialchars($paymentMethod) ?> · Paid LKR <?= number_format($paidAmount, 2) ?></p>
                <?php if ($balanceDue > 0): ?><p class="text-[10px] font-bold text-amber-700">Balance due: LKR <?= number_format($balanceDue, 2) ?></p><?php endif; ?>
            </div>
            <div class="text-right">
                <span class="text-slate-400 text-[9px] uppercase font-bold tracking-wider block">Total Amount</span>
                <span class="gv-text-primary text-2xl font-black block mt-0.5">LKR <?= number_format((float) $order['grand_total'], 2) ?></span>
            </div>
        </div>

        <!-- Minimal Footer Note -->
        <div class="mt-10 text-center text-[9px] text-slate-400 border-t border-slate-100 pt-6">
            <p class="font-bold text-slate-500">Thank you for choosing <?= htmlspecialchars($siteName) ?>!</p>
            <p class="mt-0.5">Keep this invoice for your records. Payment status is shown above.</p>
        </div>

    </div>

<script>
async function downloadInvoice(){
    var button=document.getElementById('download-invoice-btn'),invoice=document.getElementById('invoice-document');
    if(!button||!invoice||typeof html2pdf==='undefined')return;
    var old=button.innerHTML;button.disabled=true;button.classList.add('opacity-60');button.textContent='Creating PDF...';
    try{
        await html2pdf().set({
            margin:[8,8,8,8],
            filename:<?= json_encode('GiftVibeLK-Invoice-' . $receiptNo . '.pdf') ?>,
            image:{type:'jpeg',quality:0.98},
            html2canvas:{scale:2,useCORS:true,backgroundColor:'#ffffff'},
            jsPDF:{unit:'mm',format:'a4',orientation:'portrait'},
            pagebreak:{mode:['avoid-all','css','legacy']}
        }).from(invoice).save();
    }finally{button.disabled=false;button.classList.remove('opacity-60');button.innerHTML=old;}
}
</script>
</body>
</html>
