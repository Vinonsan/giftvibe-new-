<div id="purchase-auth-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-secondary/70 p-4" role="dialog" aria-modal="true" aria-labelledby="purchase-auth-title">
    <div class="max-h-[92vh] w-full max-w-lg overflow-y-auto rounded-3xl border border-primary/10 bg-white p-6 shadow-2xl sm:p-8">
        <div class="flex items-start justify-between gap-4">
            <div><p class="text-xs font-bold uppercase tracking-[.2em] text-primary">Secure checkout</p><h2 id="purchase-auth-title" class="mt-2 text-2xl font-black text-secondary">Sign in to buy</h2><p id="purchase-auth-copy" class="mt-1 text-sm text-secondary/60">Sign in or create an account to continue.</p></div>
            <button type="button" data-purchase-auth-close class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-primary/15 text-xl text-secondary hover:bg-primary/5" aria-label="Close">&times;</button>
        </div>
        <div id="purchase-auth-message" class="mt-4 hidden rounded-xl border border-accent/20 bg-accent/10 p-3 text-sm font-semibold text-accent" role="alert"></div>
        <div class="mt-6 grid grid-cols-2 rounded-xl bg-primary/5 p-1">
            <button type="button" data-auth-tab="login" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white">Sign in</button>
            <button type="button" data-auth-tab="register" class="rounded-lg px-4 py-2.5 text-sm font-bold text-secondary">Create account</button>
        </div>
        <form data-purchase-login class="mt-5 space-y-4">
            <label class="block text-sm font-bold text-secondary">Email<input required type="email" name="email" autocomplete="email" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <label class="block text-sm font-bold text-secondary">Password<input required type="password" name="password" autocomplete="current-password" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <a href="<?= htmlspecialchars(app_url('/login?reset=1')) ?>" class="block text-sm font-bold text-primary hover:text-secondary">Forgot password?</a>
            <button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white hover:bg-secondary">Sign in and continue</button>
        </form>
        <form data-purchase-register class="mt-5 hidden space-y-4">
            <fieldset><legend class="text-sm font-bold text-secondary">Choose a profile avatar</legend><div class="mt-3 grid grid-cols-6 gap-2"><?php foreach(['giftvibe-1','giftvibe-2','giftvibe-3','giftvibe-4','giftvibe-5','giftvibe-6'] as $avatarKey): ?><label class="cursor-pointer"><input type="radio" name="avatar" value="<?= $avatarKey ?>" <?= $avatarKey==='giftvibe-1'?'checked':'' ?> class="peer sr-only"><span class="block aspect-square w-full overflow-hidden rounded-xl border-2 border-primary/15 bg-slate-100 transition hover:border-primary peer-checked:border-primary peer-checked:ring-4 peer-checked:ring-primary/20"><img src="<?= htmlspecialchars(dicebear_avatar_url($avatarKey)) ?>" alt="DiceBear avatar option" loading="lazy" decoding="async" class="h-full w-full object-cover"></span></label><?php endforeach; ?></div></fieldset>
            <label class="block text-sm font-bold text-secondary">Full name<input required name="name" autocomplete="name" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <label class="block text-sm font-bold text-secondary">Email<input required type="email" name="email" autocomplete="email" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <div class="grid gap-4 sm:grid-cols-2"><?php $phoneInputName='phone';$phoneInputId='checkout-register-phone-1';$phoneInputLabel='Phone number 1';$phoneInputValue='';$phoneCodeName='phone_country_code';$phoneCodeValue='94';$phoneInputRequired=true;$phoneInputAttributes=['autocomplete'=>'tel'];require BASE_PATH.'/resources/views/components/base/phone-input.php'; ?><?php $phoneInputName='phone_2';$phoneInputId='checkout-register-phone-2';$phoneInputLabel='Phone number 2 (optional)';$phoneInputValue='';$phoneCodeName='phone_2_country_code';$phoneCodeValue='94';$phoneInputRequired=false;$phoneInputAttributes=[];require BASE_PATH.'/resources/views/components/base/phone-input.php'; ?></div>
            <label class="block text-sm font-bold text-secondary">Address line 1<input required name="address_line_1" autocomplete="street-address" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <div class="grid gap-4 sm:grid-cols-2"><label class="block text-sm font-bold text-secondary">City<input required name="city" autocomplete="address-level2" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label><label class="block text-sm font-bold text-secondary">District<input required name="district" autocomplete="address-level1" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label></div>
            <label class="block text-sm font-bold text-secondary">Password<input required minlength="8" type="password" name="password" autocomplete="new-password" class="mt-2 w-full rounded-xl border border-primary/20 px-4 py-3 outline-none focus:border-primary focus:ring-4 focus:ring-primary/10"></label>
            <button class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white hover:bg-secondary">Create account and continue</button>
        </form>
    </div>
</div>
<script>
(function(){
    var modal=document.getElementById('purchase-auth-modal'),basePath=<?= json_encode(app_base_path()) ?>,target=<?= json_encode(app_url('/shop')) ?>,message=document.getElementById('purchase-auth-message');
    function localUrl(url){if(/^https?:\/\//i.test(url)||!url.startsWith('/'))return url;if(basePath&&url!==basePath&&!url.startsWith(basePath+'/'))return basePath+url;return url;}
    function open(){modal.classList.remove('hidden');modal.classList.add('flex');document.body.classList.add('overflow-hidden');modal.querySelector('[data-purchase-login] input').focus();}
    function close(){modal.classList.add('hidden');modal.classList.remove('flex');document.body.classList.remove('overflow-hidden');}
    function tab(name){var login=name==='login';modal.querySelector('[data-purchase-login]').classList.toggle('hidden',!login);modal.querySelector('[data-purchase-register]').classList.toggle('hidden',login);modal.querySelectorAll('[data-auth-tab]').forEach(function(button){var active=button.dataset.authTab===name;button.classList.toggle('bg-primary',active);button.classList.toggle('text-white',active);button.classList.toggle('text-secondary',!active);});message.classList.add('hidden');}
    async function submit(form,url){var button=form.querySelector('button');button.disabled=true;button.classList.add('opacity-60');message.classList.add('hidden');try{var response=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(new FormData(form)))});var result=await response.json();if(result.success){window.location.assign(localUrl(target));return;}message.textContent=result.message||'Unable to continue.';message.classList.remove('hidden');}catch(error){message.textContent='Unable to continue. Please try again.';message.classList.remove('hidden');}finally{button.disabled=false;button.classList.remove('opacity-60');}}
    window.giftRequireAuth=async function(checkoutUrl){target=localUrl(checkoutUrl);try{var response=await fetch(<?= json_encode(app_url('/api/auth/status')) ?>,{headers:{'Accept':'application/json'}});var result=await response.json();if(result.logged_in){window.location.assign(localUrl(target));return;}}catch(error){}tab('login');open();};
    modal.querySelector('[data-purchase-auth-close]').addEventListener('click',close);
    modal.addEventListener('click',function(event){if(event.target===modal)close();});
    modal.querySelectorAll('[data-auth-tab]').forEach(function(button){button.addEventListener('click',function(){tab(button.dataset.authTab);});});
    modal.querySelector('[data-purchase-login]').addEventListener('submit',function(event){event.preventDefault();submit(this,<?= json_encode(app_url('/api/auth/login')) ?>);});
    modal.querySelector('[data-purchase-register]').addEventListener('submit',function(event){event.preventDefault();submit(this,<?= json_encode(app_url('/api/auth/register')) ?>);});
    document.addEventListener('keydown',function(event){if(event.key==='Escape'&&!modal.classList.contains('hidden'))close();});
})();
</script>
