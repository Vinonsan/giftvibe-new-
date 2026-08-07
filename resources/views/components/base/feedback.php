<div id="giftvibe-toast-region" class="pointer-events-none fixed right-4 top-4 z-[100] flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-2" aria-live="polite"></div>

<div id="giftvibe-confirm" class="fixed inset-0 z-[110] hidden items-center justify-center bg-secondary/40 p-4" role="dialog" aria-modal="true" aria-labelledby="giftvibe-confirm-title">
    <div class="w-full max-w-md rounded-2xl border border-primary/10 bg-white p-6 shadow-xl">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <iconify-icon icon="heroicons:question-mark-circle" width="24" height="24"></iconify-icon>
        </div>
        <h2 id="giftvibe-confirm-title" class="mt-4 text-lg font-bold text-secondary">Please confirm</h2>
        <p data-confirm-message class="mt-2 text-sm leading-6 text-secondary/70"></p>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" data-confirm-cancel class="rounded-xl border border-primary/20 px-4 py-2.5 text-sm font-bold text-secondary hover:bg-primary/5">Cancel</button>
            <button type="button" data-confirm-accept class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary/90">Confirm</button>
        </div>
    </div>
</div>

<script>
(function () {
    var region = document.getElementById('giftvibe-toast-region');
    window.GiftVibeToast = {
        show: function (message, type) {
            if (!message || !region) return;
            var toast = document.createElement('div');
            toast.className = 'pointer-events-auto flex items-start gap-3 rounded-xl border p-4 text-sm font-semibold shadow-lg ' +
                (type === 'error'
                    ? 'border-rose-200 bg-rose-50 text-rose-800'
                    : 'border-primary/20 bg-white text-secondary');
            toast.innerHTML = '<span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg ' +
                (type === 'error' ? 'bg-rose-100 text-rose-600' : 'bg-primary/10 text-primary') +
                '"><iconify-icon icon="' + (type === 'error' ? 'heroicons:exclamation-circle' : 'heroicons:check-circle') + '" width="18" height="18"></iconify-icon></span><span class="flex-1"></span>';
            toast.querySelector('span.flex-1').textContent = message;
            region.appendChild(toast);
            setTimeout(function () { toast.remove(); }, 3500);
        }
    };

    var modal = document.getElementById('giftvibe-confirm');
    var messageNode = modal.querySelector('[data-confirm-message]');
    var accept = modal.querySelector('[data-confirm-accept]');
    var cancel = modal.querySelector('[data-confirm-cancel]');
    var pending = null;
    function close(result) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        var callback = pending; pending = null;
        if (result && callback) callback();
    }
    window.GiftVibeConfirm = function (message, callback) {
        messageNode.textContent = message || 'Do you want to continue?';
        pending = callback;
        modal.classList.remove('hidden'); modal.classList.add('flex');
        cancel.focus();
    };
    accept.addEventListener('click', function () { close(true); });
    cancel.addEventListener('click', function () { close(false); });
    modal.addEventListener('click', function (event) { if (event.target === modal) close(false); });

    document.addEventListener('click', function (event) {
        var target = event.target.closest('[data-confirm]');
        if (!target || target.dataset.confirmApproved === 'true') return;
        event.preventDefault(); event.stopImmediatePropagation();
        window.GiftVibeConfirm(target.getAttribute('data-confirm'), function () {
            target.dataset.confirmApproved = 'true';
            if (target.tagName === 'A') window.location.href = target.href;
            else if (target.form && target.form.requestSubmit) target.form.requestSubmit(target);
            else target.click();
        });
    }, true);

    document.addEventListener('submit', function (event) {
        var form = event.target;
        var inline = form.getAttribute('onsubmit') || '';
        var match = inline.match(/confirm\(\s*\\?['\"]([^'\"]+)\\?['\"]\s*\)/);
        var message = form.getAttribute('data-confirm') || (match ? match[1] : '');
        if (!message || form.dataset.confirmApproved === 'true') return;
        event.preventDefault(); event.stopImmediatePropagation();
        var submitter = event.submitter;
        window.GiftVibeConfirm(message, function () {
            form.dataset.confirmApproved = 'true';
            form.removeAttribute('onsubmit');
            if (form.requestSubmit) form.requestSubmit(submitter || undefined); else form.submit();
        });
    }, true);

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-toast-message]').forEach(function (node) {
            window.GiftVibeToast.show(node.getAttribute('data-toast-message'), node.getAttribute('data-toast-type'));
        });
    });
})();
</script>
