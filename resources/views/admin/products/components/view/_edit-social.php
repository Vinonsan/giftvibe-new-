<?php declare(strict_types=1); ?>
<div class="space-y-6">
    <div>
        <div class="mb-2 flex items-center justify-between">
            <span class="text-xs font-bold text-secondary">YouTube / Video links</span>
            <button type="button" data-add-video class="rounded-lg border border-primary/20 bg-primary/5 px-3 py-1 text-[11px] font-bold text-primary hover:bg-primary/10 transition cursor-pointer">+ Add video</button>
        </div>
        <div data-video-list class="space-y-2">
            <?php foreach (($productVideos ?: ['']) as $vid): ?>
                <div data-video-row class="flex gap-2">
                    <input type="url" name="video_urls[]" value="<?= htmlspecialchars((string) $vid) ?>" placeholder="https://www.youtube.com/watch?v=…" class="<?= $fc ?>">
                    <button type="button" data-remove-video class="shrink-0 rounded-xl border border-rose-200 px-3 text-rose-500 hover:bg-rose-50 cursor-pointer">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div>
        <div class="mb-2 flex items-center justify-between">
            <span class="text-xs font-bold text-secondary">Social &amp; website links</span>
            <button type="button" id="add-social-link" class="rounded-lg border border-primary/20 bg-primary/5 px-3 py-1 text-[11px] font-bold text-primary hover:bg-primary/10 transition cursor-pointer">+ Add link</button>
        </div>
        <div id="social-links-list" class="space-y-2">
            <?php foreach ($productSocialLinks as $sl): ?>
                <div class="social-link-row flex gap-2">
                    <input type="url" name="social_url[]" value="<?= htmlspecialchars((string) ($sl['url'] ?? '')) ?>" placeholder="https://instagram.com/…" class="<?= $fc ?>">
                    <button type="button" class="remove-social shrink-0 rounded-xl border border-rose-200 px-3 text-rose-500 hover:bg-rose-50 transition cursor-pointer">&times;</button>
                </div>
            <?php endforeach; ?>
            <?php if (empty($productSocialLinks)): ?>
                <p id="no-social-msg" class="text-xs text-slate-400 italic py-2">No links yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
