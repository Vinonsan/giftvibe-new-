<?php
declare(strict_types=1);
$total = array_sum(array_column($orderItems, 'line_total'));
$field = 'w-full rounded-xl border border-primary/20 bg-white px-4 py-3 text-sm text-secondary outline-none transition placeholder:text-secondary/40 focus:border-primary focus:ring-4 focus:ring-primary/10';
?>
<section class="mx-auto w-full max-w-6xl py-10 sm:py-14">
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-[.2em] text-primary">Secure checkout</p>
        <h1 class="mt-2 text-3xl font-black text-secondary">Complete your order</h1>
        <p class="mt-2 text-sm text-secondary/65">Upload the deposit receipt. The order remains pending until an admin verifies it.</p>
    </div>
    
    <?php if ($flash): ?>
        <div class="mb-6 rounded-2xl border border-accent/20 bg-accent/10 p-4 text-sm font-semibold text-accent"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data" class="grid gap-8 lg:grid-cols-[1fr_22rem]">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="items" value="<?= htmlspecialchars($selection) ?>">
        
        <div class="space-y-6">
            <!-- Customer & Delivery Details Section -->
            <section class="rounded-3xl border border-primary/10 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-secondary">Customer and delivery details</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-semibold text-secondary">Your name<input required name="customer_name" value="<?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?>" class="mt-2 <?= $field ?>"></label>
                    <label class="text-sm font-semibold text-secondary">Email<input required type="email" name="customer_email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="mt-2 <?= $field ?>"></label>
                    <label class="text-sm font-semibold text-secondary">Your phone<input required name="customer_phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="mt-2 <?= $field ?>"></label>
                    
                    <fieldset class="sm:col-span-2">
                        <legend class="text-sm font-bold text-secondary">Who should receive this order?</legend>
                        <div class="mt-2 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-primary/15 p-4 text-sm font-semibold text-secondary"><input checked type="radio" name="recipient_type" value="self" class="accent-primary">Deliver to me</label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-primary/15 p-4 text-sm font-semibold text-secondary"><input type="radio" name="recipient_type" value="gift" class="accent-primary">Send as a gift to someone else</label>
                        </div>
                    </fieldset>
                    
                    <div id="gift-recipient-fields" class="hidden gap-4 sm:col-span-2 sm:grid-cols-2">
                        <label class="text-sm font-semibold text-secondary">Recipient name <span class="font-normal text-secondary/50">(only for gifts)</span><input name="recipient_name" class="mt-2 <?= $field ?>"></label>
                        <label class="text-sm font-semibold text-secondary">Recipient phone <span class="font-normal text-secondary/50">(only for gifts)</span><input name="recipient_phone" value="<?= htmlspecialchars($user['phone_2'] ?? '') ?>" class="mt-2 <?= $field ?>"></label>
                    </div>
                </div>
            </section>
            
            <!-- Delivery Address Selector & Editable Fields -->
            <section class="rounded-3xl border border-primary/10 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-secondary">Delivery address</h2>
                <p class="mt-1 text-sm text-secondary/60">Choose an existing address or fill out a custom delivery location below.</p>
                <input type="hidden" name="address_id" id="address-id" value="0">
                
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <label class="cursor-pointer rounded-2xl border border-primary/15 bg-primary/5 p-4 has-[:checked]:border-primary has-[:checked]:ring-2 has-[:checked]:ring-primary/20">
                        <span class="flex gap-3">
                            <input checked type="radio" name="delivery_address_choice" value="account" class="mt-1 accent-primary">
                            <span class="text-sm text-secondary">
                                <strong class="block text-primary">Account address</strong>
                                <span class="mt-1 block"><?=htmlspecialchars($user['address_line_1']??($user['address']??''))?>, <?=htmlspecialchars($user['city']??'')?>, <?=htmlspecialchars($user['district']??'')?></span>
                            </span>
                        </span>
                    </label>
                    
                    <?php foreach($savedAddresses as $address): ?>
                        <label class="cursor-pointer rounded-2xl border border-primary/15 bg-primary/5 p-4 has-[:checked]:border-primary has-[:checked]:ring-2 has-[:checked]:ring-primary/20">
                            <span class="flex gap-3">
                                <input type="radio" name="delivery_address_choice" value="saved:<?=(int)$address['id']?>" class="mt-1 accent-primary">
                                <span class="text-sm text-secondary">
                                    <strong class="block text-primary"><?=htmlspecialchars($address['label'])?></strong>
                                    <span class="mt-1 block"><?=htmlspecialchars($address['address_line_1'])?>, <?=htmlspecialchars($address['city'])?>, <?=htmlspecialchars($address['district'])?></span>
                                </span>
                            </span>
                        </label>
                    <?php endforeach; ?>
                    
                    <label class="cursor-pointer rounded-2xl border border-primary/15 bg-white p-4 has-[:checked]:border-primary has-[:checked]:ring-2 has-[:checked]:ring-primary/20">
                        <span class="flex gap-3">
                            <input type="radio" name="delivery_address_choice" value="new" class="mt-1 accent-primary">
                            <span class="text-sm font-bold text-primary">+ Add new address</span>
                        </span>
                    </label>
                </div>
                
                <!-- Editable Address inputs (Always visible and pre-populated dynamically) -->
                <div id="address-fields-block" class="mt-6 border-t border-slate-100 pt-5 space-y-4">
                    <p class="text-xs font-bold text-primary uppercase tracking-wider">Confirm Address Fields</p>
                    
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="text-sm font-semibold text-secondary sm:col-span-2">Address line 1<input required name="delivery_address_line_1" value="" class="mt-2 <?= $field ?>"></label>
                        <label class="text-sm font-semibold text-secondary sm:col-span-2">Address line 2<input name="delivery_address_line_2" class="mt-2 <?= $field ?>"></label>
                        <label class="text-sm font-semibold text-secondary">City<input required name="delivery_city" value="" class="mt-2 <?= $field ?>"></label>
                        <label class="text-sm font-semibold text-secondary">District<input required name="delivery_district" value="" class="mt-2 <?= $field ?>"></label>
                    </div>

                    <div id="save-address-wrapper" class="hidden">
                        <label class="text-sm font-semibold text-secondary block mb-3">Address label<input name="address_label" placeholder="Home, Office..." class="mt-2 <?= $field ?>"></label>
                        <label class="flex items-center gap-2 text-sm font-semibold text-secondary">
                            <input type="checkbox" name="save_address" value="1" class="accent-primary">Save this address for future orders
                        </label>
                    </div>
                </div>
            </section>
            
            <!-- Payment Section -->
            <section class="rounded-3xl border border-primary/10 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-secondary">Payment</h2>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-primary/20 p-4 text-sm font-bold text-secondary"><input checked type="radio" name="payment_method" value="cod" class="accent-primary">Cash on delivery</label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-primary/20 p-4 text-sm font-bold text-secondary"><input type="radio" name="payment_method" value="bank_deposit" class="accent-primary">Bank deposit</label>
                </div>
                
                <div id="cod-message" class="mt-4 rounded-2xl border border-accent/20 bg-accent/10 p-4 text-sm font-semibold text-accent">Cash on delivery requires a Rs. 500 advance payment. The remaining balance is paid when the order is delivered.</div>
                
                <?php if($bankAccounts): ?>
                    <div class="mt-5">
                        <p class="text-sm font-bold text-secondary">Choose a deposit account</p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <?php foreach($bankAccounts as $index=>$bank): ?>
                                <label class="cursor-pointer rounded-2xl border border-primary/15 bg-primary/5 p-4 text-sm text-secondary has-[:checked]:border-primary has-[:checked]:ring-2 has-[:checked]:ring-primary/20">
                                    <span class="flex items-start gap-3">
                                        <input required type="radio" name="bank_account_id" value="<?= (int)$bank['id'] ?>" <?= $index===0?'checked':'' ?> class="mt-1 accent-primary">
                                        <span>
                                            <strong class="block text-primary"><?=htmlspecialchars($bank['bank_name'])?></strong>
                                            <span class="mt-1 block font-semibold"><?=htmlspecialchars($bank['account_name'])?></span>
                                            <span class="block"><?=htmlspecialchars($bank['account_number'])?></span>
                                            <?php if($bank['branch']):?>
                                                <span class="block text-secondary/60"><?=htmlspecialchars($bank['branch'])?></span>
                                            <?php endif;?>
                                        </span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-5 rounded-2xl border border-accent/20 bg-accent/10 p-4 text-sm font-semibold text-accent">No bank account is configured. Please contact GiftVibe before placing this order.</div>
                <?php endif; ?>
                
                <label class="mt-5 block text-sm font-semibold text-secondary">Payment receipt (JPG, PNG, WebP, or PDF)<input required type="file" name="receipt" accept="image/jpeg,image/png,image/webp,application/pdf" class="mt-2 block w-full rounded-xl border border-primary/20 bg-primary/5 p-3 text-sm text-secondary file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:font-bold file:text-white"></label>
                <label class="mt-5 block text-sm font-semibold text-secondary">Order notes<textarea name="customer_notes" rows="3" class="mt-2 <?= $field ?>"></textarea></label>
            </section>
        </div>
        
        <!-- Sidebar Order Summary -->
        <aside>
            <div class="sticky top-24 rounded-3xl border border-primary/10 bg-white p-6 shadow-lg">
                <h2 class="text-lg font-bold text-secondary">Order summary</h2>
                <div class="mt-5 divide-y divide-primary/10 border-b border-primary/10">
                    <?php foreach($orderItems as $product): ?>
                        <div class="py-3">
                            <div class="flex justify-between gap-3">
                                <p class="text-sm font-semibold text-secondary"><?= htmlspecialchars($product['name']) ?> × <?= (int)$product['quantity'] ?></p>
                                <p class="shrink-0 text-sm font-bold text-secondary">LKR <?= number_format((float)$product['line_total'],2) ?></p>
                            </div>
                            <p class="mt-1 text-xs text-secondary/55">LKR <?= number_format((float)$product['base_price'],2) ?> each</p>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="flex justify-between pt-5 text-sm font-semibold text-secondary/60">
                    <span>Order total</span>
                    <span>LKR <?= number_format($total, 2) ?></span>
                </div>
                
                <div class="mt-4 rounded-2xl bg-primary/5 p-4 text-center">
                    <p class="text-xs font-bold uppercase tracking-wider text-secondary/55">Pay now</p>
                    <p id="pay-now-amount" class="mt-1 text-2xl font-black text-primary">LKR <?= number_format(min(500,$total),2) ?></p>
                </div>
                
                <button class="mt-4 w-full rounded-xl bg-primary px-5 py-3.5 text-sm font-bold text-white transition hover:bg-secondary">Confirm payment & submit</button>
                <p class="mt-3 text-center text-xs leading-5 text-secondary/55">Receipt will be sent to admin for verification. Confirmation follows after review.</p>
            </div>
        </aside>
    </form>
</section>

<script>
(function(){
    var total = <?=json_encode((float)$total)?>,
        amount = document.getElementById('pay-now-amount'),
        codMessage = document.getElementById('cod-message'),
        giftFields = document.getElementById('gift-recipient-fields'),
        addressId = document.getElementById('address-id'),
        saveAddressWrapper = document.getElementById('save-address-wrapper');
        
    var addrLine1 = document.querySelector('input[name="delivery_address_line_1"]');
    var addrLine2 = document.querySelector('input[name="delivery_address_line_2"]');
    var addrCity = document.querySelector('input[name="delivery_city"]');
    var addrDistrict = document.querySelector('input[name="delivery_district"]');
    
    var accountAddress = {
        line1: <?= json_encode($user['address_line_1'] ?? ($user['address'] ?? '')) ?>,
        line2: <?= json_encode($user['address_line_2'] ?? '') ?>,
        city: <?= json_encode($user['city'] ?? '') ?>,
        district: <?= json_encode($user['district'] ?? '') ?>
    };
    
    var savedAddresses = <?= json_encode($savedAddresses) ?>;

    function updatePayment(){
        var method = document.querySelector('input[name="payment_method"]:checked').value,
            pay = method === 'cod' ? Math.min(500, total) : total;
        amount.textContent = 'LKR ' + pay.toLocaleString('en-LK',{minimumFractionDigits:2,maximumFractionDigits:2});
        codMessage.classList.toggle('hidden', method !== 'cod');
    }
    
    function updateRecipient(){
        var gift = document.querySelector('input[name="recipient_type"]:checked').value === 'gift';
        giftFields.classList.toggle('hidden', !gift);
        giftFields.classList.toggle('grid', gift);
        giftFields.querySelectorAll('input').forEach(function(input){
            input.required = gift;
        });
    }
    
    function updateAddress(){
        var choice = document.querySelector('input[name="delivery_address_choice"]:checked').value;
        if (choice === 'account') {
            addressId.value = '0';
            addrLine1.value = accountAddress.line1;
            addrLine2.value = accountAddress.line2;
            addrCity.value = accountAddress.city;
            addrDistrict.value = accountAddress.district;
            saveAddressWrapper.classList.add('hidden');
        } else if (choice === 'new') {
            addressId.value = '0';
            addrLine1.value = '';
            addrLine2.value = '';
            addrCity.value = '';
            addrDistrict.value = '';
            saveAddressWrapper.classList.remove('hidden');
        } else if (choice.indexOf('saved:') === 0) {
            var id = parseInt(choice.split(':')[1], 10);
            addressId.value = String(id);
            var matched = savedAddresses.find(function(a) { return parseInt(a.id, 10) === id; });
            if (matched) {
                addrLine1.value = matched.address_line_1 || '';
                addrLine2.value = matched.address_line_2 || '';
                addrCity.value = matched.city || '';
                addrDistrict.value = matched.district || '';
            }
            saveAddressWrapper.classList.add('hidden');
        }
    }
    
    document.querySelectorAll('input[name="payment_method"]').forEach(function(input){
        input.addEventListener('change', updatePayment);
    });
    document.querySelectorAll('input[name="recipient_type"]').forEach(function(input){
        input.addEventListener('change', updateRecipient);
    });
    document.querySelectorAll('input[name="delivery_address_choice"]').forEach(function(input){
        input.addEventListener('change', updateAddress);
    });
    
    updatePayment();
    updateRecipient();
    updateAddress();
})();
</script>
