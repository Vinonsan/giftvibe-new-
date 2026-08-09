<script>
(function () {
    function enhance(input) {
        if (!input || input.dataset.eyeReady === 'true') return;
        input.dataset.eyeReady = 'true';
        var wrapper = document.createElement('div');
        wrapper.className = 'relative mt-2';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        input.classList.remove('mt-2');
        input.classList.add('pr-12');

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-primary';
        button.setAttribute('aria-label', 'Show password');
        button.innerHTML = '<svg data-eye-open class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.75"/></svg><svg data-eye-closed class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 5.5A10.5 10.5 0 0 1 12 5.25c6 0 9.75 6.75 9.75 6.75a15 15 0 0 1-2.1 3.1M6.3 6.3C3.7 8 2.25 12 2.25 12S6 18.75 12 18.75c1.2 0 2.3-.27 3.3-.7"/></svg>';
        button.addEventListener('click', function () {
            var visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            button.querySelector('[data-eye-open]').classList.toggle('hidden', !visible);
            button.querySelector('[data-eye-closed]').classList.toggle('hidden', visible);
            button.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
        });
        wrapper.appendChild(button);
    }
    function scan(root) { (root || document).querySelectorAll('input[type="password"]').forEach(enhance); }
    document.addEventListener('DOMContentLoaded', function () {
        scan(document);
        new MutationObserver(function (changes) { changes.forEach(function (change) { change.addedNodes.forEach(function (node) { if (node.nodeType === 1) scan(node); }); }); }).observe(document.body, {childList:true,subtree:true});
    });
})();
</script>
