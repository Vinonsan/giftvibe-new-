<section class="bg-slate-50 py-10">
    <div class="mx-auto max-w-container px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <div class="rounded-card border border-slate-200 bg-white p-6 shadow-card">
                    <p class="text-xs font-bold uppercase tracking-wider text-primary">Delivery addresses</p>
                    <h1 class="mt-2 text-2xl font-extrabold text-slate-950">Add address</h1>
                    <?php if (!empty($message)): ?><div class="mt-4 rounded-card border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700"><?= e($message) ?></div><?php endif; ?>
                    <?php if (!empty($error)): ?><div class="mt-4 rounded-card border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-danger"><?= e($error) ?></div><?php endif; ?>
                    <form method="POST" action="<?= e(url('/customer/addresses')) ?>" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <?= csrf_field() ?>
                        <input name="label" placeholder="Label, e.g. Home" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                        <input name="recipient_name" required placeholder="Recipient name" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                        <input name="phone" required placeholder="Phone" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                        <input name="city" required placeholder="City" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                        <input name="district" required placeholder="District" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                        <input name="province" placeholder="Province" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                        <input name="address_line_1" required placeholder="Address line 1" class="rounded-button border border-slate-300 px-3 py-2 text-sm sm:col-span-2">
                        <input name="address_line_2" placeholder="Address line 2" class="rounded-button border border-slate-300 px-3 py-2 text-sm sm:col-span-2">
                        <input name="postal_code" placeholder="Postal code" class="rounded-button border border-slate-300 px-3 py-2 text-sm">
                        <label class="flex items-center gap-2 rounded-button border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-600"><input type="checkbox" name="is_default" value="1"> Default</label>
                        <button class="rounded-button bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-primary-700 sm:col-span-2">Save Address</button>
                    </form>
                </div>
            </div>
            <div class="lg:col-span-7">
                <div class="grid gap-4">
                    <?php foreach (($addresses ?? []) as $address): ?>
                        <article class="rounded-card border border-slate-200 bg-white p-5 shadow-card">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="font-extrabold text-slate-950"><?= e($address['label']) ?></h2>
                                        <?php if ($address['is_default']): ?><span class="rounded-full bg-primary-50 px-2 py-0.5 text-xs font-bold text-primary">Default</span><?php endif; ?>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-600"><?= e($address['recipient_name']) ?> · <?= e($address['phone']) ?></p>
                                    <p class="mt-1 text-sm text-slate-500"><?= e($address['address_line_1']) ?> <?= e($address['address_line_2']) ?>, <?= e($address['city']) ?>, <?= e($address['district']) ?></p>
                                </div>
                                <div class="flex gap-2">
                                    <?php if (!$address['is_default']): ?>
                                        <form method="POST" action="<?= e(url('/customer/addresses/' . $address['id'] . '/default')) ?>"><?= csrf_field() ?><button class="rounded-button border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700">Make default</button></form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?= e(url('/customer/addresses/' . $address['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this address?');"><?= csrf_field() ?><button class="rounded-button border border-red-200 px-3 py-2 text-xs font-bold text-danger">Delete</button></form>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    <?php if (empty($addresses)): ?>
                        <div class="rounded-card border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 shadow-card">No addresses yet. Add your first delivery address.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
