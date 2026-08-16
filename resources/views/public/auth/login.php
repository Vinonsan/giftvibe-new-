<?php declare(strict_types=1); ?>
<section class="mx-auto w-full max-w-md py-14">
    <div class="rounded-3xl border border-primary/10 bg-white p-7 shadow-xl">
        <p class="text-xs font-bold uppercase tracking-[.2em] text-primary">Customer account</p>
        <h1 class="mt-2 text-2xl font-black text-secondary">Sign in to continue</h1>
        <p class="mt-2 text-sm text-secondary/60"><?= htmlspecialchars($notice ?: 'Sign in before placing your order.') ?></p>
        <div id="login-message" class="mt-4 hidden rounded-xl border border-accent/20 bg-accent/10 p-3 text-sm font-semibold text-accent" role="alert"></div>
        <form id="customer-login" class="mt-6 space-y-4">
            <label class="block text-sm font-bold text-secondary">Email<input required type="email" name="email" autocomplete="email" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <label class="block text-sm font-bold text-secondary">Password<input required type="password" name="password" autocomplete="current-password" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <button type="button" data-reset-open class="block text-sm font-bold text-primary hover:text-secondary">Forgot password?</button>
            <button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white transition hover:bg-secondary">Sign in</button>
        </form>
        <p class="mt-5 text-center text-sm text-secondary/60">New customer? <button type="button" data-register-open class="font-bold text-primary hover:text-secondary">Create an account</button></p>
    </div>
</section>

<div id="register-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-secondary/70 p-4" role="dialog" aria-modal="true" aria-labelledby="register-title">
    <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl border border-primary/10 bg-white p-6 shadow-2xl sm:p-8">
        <div class="flex items-start justify-between gap-4"><div><p class="text-xs font-bold uppercase tracking-[.2em] text-primary">New customer</p><h2 id="register-title" class="mt-2 text-2xl font-black text-secondary">Create your account</h2><p class="mt-1 text-sm text-secondary/60">Enter your details once; they will be prefilled at checkout.</p></div><button type="button" data-register-close class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-primary/15 text-xl text-secondary transition hover:bg-primary/5" aria-label="Close registration modal">&times;</button></div>
        <div id="register-message" class="mt-4 hidden rounded-xl border border-accent/20 bg-accent/10 p-3 text-sm font-semibold text-accent" role="alert"></div>
        <form id="customer-register" class="mt-6 space-y-4">
            <label class="block text-sm font-bold text-secondary">Full name<input required name="name" autocomplete="name" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <label class="block text-sm font-bold text-secondary">Email<input required type="email" name="email" autocomplete="email" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <div class="grid gap-4 sm:grid-cols-2"><?php $phoneInputName='phone';$phoneInputId='register-phone-1';$phoneInputLabel='Phone number 1';$phoneInputValue='';$phoneCodeName='phone_country_code';$phoneCodeValue='94';$phoneInputRequired=true;$phoneInputAttributes=['autocomplete'=>'tel'];require BASE_PATH.'/resources/views/components/base/phone-input.php'; ?><?php $phoneInputName='phone_2';$phoneInputId='register-phone-2';$phoneInputLabel='Phone number 2 (optional)';$phoneInputValue='';$phoneCodeName='phone_2_country_code';$phoneCodeValue='94';$phoneInputRequired=false;$phoneInputAttributes=[];require BASE_PATH.'/resources/views/components/base/phone-input.php'; ?></div>
            <label class="block text-sm font-bold text-secondary">Address line 1<input required name="address_line_1" autocomplete="street-address" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <div class="grid gap-4 sm:grid-cols-2"><label class="block text-sm font-bold text-secondary">City<input required name="city" autocomplete="address-level2" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label><label class="block text-sm font-bold text-secondary">District<input required name="district" autocomplete="address-level1" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label></div>
            <label class="block text-sm font-bold text-secondary">Password<input required minlength="8" type="password" name="password" autocomplete="new-password" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"><span class="mt-1 block text-xs font-normal text-secondary/50">Minimum 8 characters</span></label>
            <button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white transition hover:bg-secondary">Create account and continue</button>
        </form>
    </div>
</div>

<div id="reset-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-secondary/70 p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-md rounded-3xl bg-white p-7 shadow-2xl">
        <div class="flex justify-between gap-4"><div><h2 class="text-xl font-black text-secondary">Reset password</h2><p class="mt-1 text-sm text-secondary/60">Verify your email or phone with a one-time code.</p></div><button type="button" data-reset-close class="h-9 w-9 rounded-full border text-xl">&times;</button></div>
        <div id="reset-message" class="mt-4 hidden rounded-xl bg-primary/10 p-3 text-sm font-semibold text-primary"></div>
        <form id="reset-request" class="mt-5 space-y-4"><label class="block text-sm font-bold text-secondary">Email or phone<input required name="identifier" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3"></label><div class="grid grid-cols-2 gap-3"><label class="rounded-xl border p-3 text-sm font-semibold"><input checked type="radio" name="channel" value="email"> Email OTP</label><label class="rounded-xl border p-3 text-sm font-semibold"><input type="radio" name="channel" value="sms"> SMS OTP</label></div><button class="w-full rounded-xl bg-primary px-5 py-3 font-bold text-white">Send OTP</button></form>
        <form id="reset-verify" class="mt-5 hidden space-y-4"><label class="block text-sm font-bold text-secondary">6-digit OTP<input required name="otp" inputmode="numeric" maxlength="6" pattern="[0-9]{6}" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 tracking-[.4em]"></label><button class="w-full rounded-xl bg-primary px-5 py-3 font-bold text-white">Verify OTP</button></form>
        <form id="reset-password" class="mt-5 hidden space-y-4"><label class="block text-sm font-bold text-secondary">New password<input required minlength="8" type="password" name="password" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3"></label><label class="block text-sm font-bold text-secondary">Confirm password<input required minlength="8" type="password" name="password_confirmation" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3"></label><button class="w-full rounded-xl bg-primary px-5 py-3 font-bold text-white">Reset password</button></form>
    </div>
</div>

<script>
(function(){
    var redirect=<?= json_encode(app_url($redirect), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;
    var modal=document.getElementById('register-modal');
    var loginMessage=document.getElementById('login-message');
    var registerMessage=document.getElementById('register-message');
    var resetModal=document.getElementById('reset-modal'),resetMessage=document.getElementById('reset-message');
    function setModal(open){modal.classList.toggle('hidden',!open);modal.classList.toggle('flex',open);document.body.classList.toggle('overflow-hidden',open);if(open)modal.querySelector('input[name="name"]').focus();}
    async function submit(form,url,message){
        message.classList.add('hidden');
        var button=form.querySelector('button[type="submit"],button:not([type])');
        button.disabled=true;button.classList.add('opacity-60');
        try{var response=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(new FormData(form)))});var result=await response.json();if(result.success){window.location.href=redirect;return;}message.textContent=result.message||'Authentication failed.';message.classList.remove('hidden');}
        catch(error){message.textContent='Unable to continue. Please try again.';message.classList.remove('hidden');}
        finally{button.disabled=false;button.classList.remove('opacity-60');}
    }
    document.querySelector('[data-register-open]').addEventListener('click',function(){setModal(true);});
    document.querySelector('[data-register-close]').addEventListener('click',function(){setModal(false);});
    modal.addEventListener('click',function(event){if(event.target===modal)setModal(false);});
    document.addEventListener('keydown',function(event){if(event.key==='Escape')setModal(false);});
    document.getElementById('customer-login').addEventListener('submit',function(event){event.preventDefault();submit(this,<?= json_encode(app_url('/api/auth/login')) ?>,loginMessage);});
    document.getElementById('customer-register').addEventListener('submit',function(event){event.preventDefault();submit(this,<?= json_encode(app_url('/api/auth/register')) ?>,registerMessage);});
    function resetNotice(text){resetMessage.textContent=text;resetMessage.classList.remove('hidden');}
    document.querySelector('[data-reset-open]').addEventListener('click',function(){resetModal.classList.remove('hidden');resetModal.classList.add('flex');});
    document.querySelector('[data-reset-close]').addEventListener('click',function(){resetModal.classList.add('hidden');resetModal.classList.remove('flex');});
    async function resetCall(form,url){var response=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(new FormData(form)))});var result=await response.json();resetNotice(result.message||(!result.success?'Unable to continue.':''));return result;}
    document.getElementById('reset-request').addEventListener('submit',async function(event){event.preventDefault();var result=await resetCall(this,<?= json_encode(app_url('/api/auth/password/request')) ?>);if(result.success){this.classList.add('hidden');document.getElementById('reset-verify').classList.remove('hidden');}});
    document.getElementById('reset-verify').addEventListener('submit',async function(event){event.preventDefault();var result=await resetCall(this,<?= json_encode(app_url('/api/auth/password/verify')) ?>);if(result.success){this.classList.add('hidden');document.getElementById('reset-password').classList.remove('hidden');}});
    document.getElementById('reset-password').addEventListener('submit',async function(event){event.preventDefault();var result=await resetCall(this,<?= json_encode(app_url('/api/auth/password/reset')) ?>);if(result.success){setTimeout(function(){resetModal.classList.add('hidden');resetModal.classList.remove('flex');},1200);}});
    if(new URLSearchParams(window.location.search).get('reset')==='1')document.querySelector('[data-reset-open]').click();
})();
</script>
