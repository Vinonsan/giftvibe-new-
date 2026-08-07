<?php
declare(strict_types=1);
?>
<?php if ($customerOrders): ?>
    <div class="overflow-hidden rounded-xl border border-slate-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-semibold">Order #</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold">Payment</th>
                    <th class="px-4 py-3 font-semibold">Recipient</th>
                    <th class="px-4 py-3 font-semibold text-right">Total</th>
                    <th class="px-4 py-3 font-semibold text-right">Date</th>
                    <th class="px-4 py-3 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($customerOrders as $order): ?>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-secondary"><?= htmlspecialchars((string) $order['order_number']) ?></td>
                        <td class="px-4 py-3"><?= $orderStatusBadge((string) $order['order_status']) ?></td>
                        <td class="px-4 py-3 text-xs font-semibold capitalize text-slate-600"><?= htmlspecialchars((string) $order['payment_status']) ?></td>
                        <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars((string) ($order['recipient_name'] ?? '—')) ?></td>
                        <td class="px-4 py-3 text-right font-semibold text-secondary">LKR <?= number_format((float) $order['grand_total'], 2) ?></td>
                        <td class="px-4 py-3 text-right text-xs text-slate-400"><?= htmlspecialchars(substr((string) ($order['created_at'] ?? ''), 0, 10)) ?></td>
                        <td class="px-4 py-3 text-right">
                            <a href="/admin/orders/view?id=<?= (int) $order['id'] ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-primary/10 text-secondary/60 hover:text-primary" title="View order">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.25"/></svg>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="rounded-xl border border-dashed border-slate-200 py-10 text-center italic text-slate-400">This customer has not placed any orders yet.</p>
<?php endif; ?>
