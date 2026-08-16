<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    var FC = <?= json_encode($fc ?? 'w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition bg-white') ?>;

    /* Keywords */
    var hiddenKw = document.getElementById('gv-keywords-hidden');
    var tagsCont = document.getElementById('gv-tags-container');
    var tagInput = document.getElementById('gv-tag-input');
    var tags = [];
    if (hiddenKw && hiddenKw.value.trim()) {
        tags = hiddenKw.value.split(',').map(function (t) { return t.trim().replace(/^#+/, ''); }).filter(Boolean);
    }
    function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;'); }
    function renderTags() {
        if (!tagsCont) return;
        tagsCont.innerHTML = '';
        tags.forEach(function (tag, i) {
            var span = document.createElement('span');
            span.className = 'inline-flex items-center gap-1 rounded-lg bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary';
            span.innerHTML = '#' + esc(tag) + '<button type="button" data-ti="' + i + '" class="ml-0.5 leading-none cursor-pointer hover:text-rose-600">&times;</button>';
            tagsCont.appendChild(span);
        });
        if (hiddenKw) hiddenKw.value = tags.join(', ');
    }
    tagsCont && tagsCont.addEventListener('click', function (e) {
        var b = e.target.closest('[data-ti]');
        if (b) { tags.splice(parseInt(b.getAttribute('data-ti'), 10), 1); renderTags(); }
    });
    tagInput && tagInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            var v = tagInput.value.trim().replace(/^#+/, '');
            if (v && !tags.includes(v)) { tags.push(v); renderTags(); }
            tagInput.value = '';
        }
    });
    renderTags();

    /* Videos */
    var videoList = document.querySelector('[data-video-list]');
    function makeVideoRow() {
        var row = document.createElement('div');
        row.dataset.videoRow = '';
        row.className = 'flex gap-2';
        row.innerHTML = '<input type="url" name="video_urls[]" placeholder="https://www.youtube.com/watch?v=…" class="' + FC + '">' +
            '<button type="button" data-remove-video class="shrink-0 rounded-xl border border-rose-200 px-3 text-rose-500 hover:bg-rose-50 cursor-pointer">&times;</button>';
        return row;
    }
    function bindVideoRemove(btn) {
        btn.addEventListener('click', function () {
            var rows = videoList ? videoList.querySelectorAll('[data-video-row]') : [];
            if (rows.length > 1) btn.closest('[data-video-row]').remove();
            else { var inp = btn.closest('[data-video-row]').querySelector('input'); if (inp) inp.value = ''; }
        });
    }
    videoList && videoList.querySelectorAll('[data-remove-video]').forEach(bindVideoRemove);
    document.querySelector('[data-add-video]')?.addEventListener('click', function () {
        var row = makeVideoRow();
        videoList.appendChild(row);
        bindVideoRemove(row.querySelector('[data-remove-video]'));
    });

    /* Social links */
    var socialList = document.getElementById('social-links-list');
    var noSocialMsg = document.getElementById('no-social-msg');
    function makeSocialRow(url) {
        var row = document.createElement('div');
        row.className = 'social-link-row flex gap-2';
        row.innerHTML = '<input type="url" name="social_url[]" value="' + (url || '') + '" placeholder="https://instagram.com/…" class="' + FC + '">' +
            '<button type="button" class="remove-social shrink-0 rounded-xl border border-rose-200 px-3 text-rose-500 hover:bg-rose-50 transition cursor-pointer">&times;</button>';
        row.querySelector('.remove-social').addEventListener('click', function () {
            row.remove();
            if (socialList && socialList.querySelectorAll('.social-link-row').length === 0 && noSocialMsg) noSocialMsg.classList.remove('hidden');
        });
        return row;
    }
    socialList && socialList.querySelectorAll('.remove-social').forEach(function (btn) {
        btn.addEventListener('click', function () {
            btn.closest('.social-link-row').remove();
            if (socialList.querySelectorAll('.social-link-row').length === 0 && noSocialMsg) noSocialMsg.classList.remove('hidden');
        });
    });
    document.getElementById('add-social-link')?.addEventListener('click', function () {
        if (noSocialMsg) noSocialMsg.classList.add('hidden');
        socialList.appendChild(makeSocialRow(''));
    });

    /* Profit */
    function calcProfit() {
        var s = parseFloat(document.getElementById('selling-price')?.value) || 0;
        var b = parseFloat(document.getElementById('buying-price')?.value) || 0;
        var disp = document.getElementById('profit-display');
        if (!disp) return;
        var p = s - b;
        disp.textContent = 'LKR ' + p.toFixed(2);
        disp.className = 'mt-0.5 text-2xl font-black ' + (p >= 0 ? 'text-emerald-600' : 'text-rose-500');
    }
    document.getElementById('selling-price')?.addEventListener('input', calcProfit);
    document.getElementById('buying-price')?.addEventListener('input', calcProfit);
    calcProfit();
});
</script>
