<?php
declare(strict_types=1);
/**
 * Ultra-modern print-ready invoice receipt template.
 *
 * @var array $order
 * @var array $items
 * @var array $settings
 */
$receiptNo = 'GON-' . str_pad((string)$order['id'], 2, '0', STR_PAD_LEFT);
$logo = $settings['site_logo'] ?? '/assets/images/logo.svg';
$siteName = $settings['site_name'] ?? 'GiftVibe';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?= htmlspecialchars($receiptNo) ?></title>
    <script src="<?= htmlspecialchars(app_url('/assets/js/tailwindcss-browser.js')) ?>"></script>
    <style>
        @media print {
            body { background: white; color: black; padding: 0; }
            .no-print { display: none; }
            .print-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-50/30 font-sans text-slate-800 antialiased p-4 sm:p-8">

    <!-- Top floating action bar (Hidden on print) -->
    <div class="no-print mx-auto max-w-xl mb-6 flex justify-between items-center bg-white border border-slate-100 p-4 rounded-2xl shadow-sm">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Official Invoice Receipt</span>
        <div class="flex gap-2">
            <button onclick="window.print()" class="rounded-xl bg-rose-500 px-4.5 py-2.5 text-xs font-bold text-white hover:bg-rose-600 transition-all cursor-pointer flex items-center gap-1.5 shadow-md shadow-rose-500/10">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-10.56 0a2.44 2.44 0 0 1-1.956-2.4L4.5 5.25h15l-.204 6.171a2.44 2.44 0 0 1-1.956 2.4m-10.56 0h10.56M12 3v3.75m0 0a1.5 1.5 0 0 1-3 0h6a1.5 1.5 0 0 1-3 0Z"/></svg>
                <span>Print Invoice</span>
            </button>
            <button onclick="window.close()" class="rounded-xl border border-slate-200 bg-white px-4.5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                Close Window
            </button>
        </div>
    </div>

    <!-- Printable Receipt Card -->
    <div class="mx-auto max-w-xl bg-white rounded-[2rem] border border-slate-100 p-8 sm:p-10 shadow-lg shadow-slate-100/40 relative print-card">
        
        <!-- Brand Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-6 mb-6">
            <div class="flex items-center gap-2.5">
                <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($siteName) ?> Logo" class="h-9 w-9 rounded-xl object-contain">
                <div>
                    <h1 class="text-sm font-black text-slate-900 uppercase tracking-wider"><?= htmlspecialchars($siteName) ?></h1>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">Official Receipt</span>
                </div>
            </div>
            <div class="text-right text-xs">
                <span class="text-xs font-black text-rose-500 block"><?= htmlspecialchars($receiptNo) ?></span>
                <span class="text-[10px] text-slate-400 block mt-0.5"><?= htmlspecialchars(substr($order['created_at'], 0, 10)) ?></span>
            </div>
        </div>

        <!-- Customer & Order details -->
        <div class="grid grid-cols-2 gap-6 text-xs mb-6 bg-slate-50/50 rounded-2xl p-4.5 border border-slate-100/50">
            <div>
                <p class="font-bold text-rose-500 uppercase tracking-wider mb-1.5 text-[9px]">Billing To</p>
                <p class="font-bold text-slate-800"><?= htmlspecialchars($order['customer_name']) ?></p>
                <p class="text-slate-500 mt-0.5"><?= htmlspecialchars($order['customer_phone']) ?></p>
            </div>
            <div>
                <p class="font-bold text-rose-500 uppercase tracking-wider mb-1.5 text-[9px]">Shipping To</p>
                <p class="font-bold text-slate-800"><?= htmlspecialchars($order['recipient_name']) ?></p>
                <p class="text-slate-500 mt-0.5"><?= htmlspecialchars($order['recipient_phone']) ?></p>
                <p class="text-slate-500 leading-normal truncate" title="<?= htmlspecialchars($order['delivery_address_line_1'] . ', ' . $order['delivery_city'] . ', ' . $order['delivery_district']) ?>"><?= htmlspecialchars($order['delivery_city']) ?></p>
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
                                        <small class="block text-[9px] font-bold text-rose-500 mt-0.5">Colour: <?= htmlspecialchars((string) $options['colour']) ?></small>
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
            <div>
                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 font-bold text-emerald-600 text-[10px] uppercase">Paid</span>
            </div>
            <div class="text-right">
                <span class="text-slate-400 text-[9px] uppercase font-bold tracking-wider block">Total Amount</span>
                <span class="text-xl font-black text-rose-500 block mt-0.5">LKR <?= number_format((float) $order['grand_total'], 2) ?></span>
            </div>
        </div>

        <!-- Minimal Footer Note -->
        <div class="mt-10 text-center text-[9px] text-slate-400 border-t border-slate-100 pt-6">
            <p class="font-bold text-slate-500">Thank you for choosing <?= htmlspecialchars($siteName) ?>!</p>
            <p class="mt-0.5">This document serves as validation of confirmed payment receipt.</p>
        </div>

    </div>

</body>
</html>
