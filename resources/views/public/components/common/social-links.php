<?php
/**
 * Variables:
 * @var string|null $class
 */
?>
<div class="flex gap-4 text-slate-400 <?= $class ?? '' ?>">
    <a href="#" class="hover:text-white transition-colors" aria-label="Facebook">
        <?php component('public/components/common/icon', ['name' => 'facebook', 'size' => 'md']); ?>
    </a>
    <a href="#" class="hover:text-white transition-colors" aria-label="Instagram">
        <?php component('public/components/common/icon', ['name' => 'instagram', 'size' => 'md']); ?>
    </a>
    <a href="#" class="hover:text-white transition-colors" aria-label="TikTok">
        <?php component('public/components/common/icon', ['name' => 'tiktok', 'size' => 'md']); ?>
    </a>
    <a href="#" class="hover:text-white transition-colors" aria-label="LinkedIn">
        <?php component('public/components/common/icon', ['name' => 'linkedin', 'size' => 'md']); ?>
    </a>
</div>