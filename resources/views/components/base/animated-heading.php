<?php

declare(strict_types=1);

$animatedHeadingTitle = (string) ($animatedHeadingTitle ?? 'Gifts made for every moment');
$animatedHeadingEyebrow = (string) ($animatedHeadingEyebrow ?? 'Discover GiftVibe');
$animatedHeadingDescription = (string) ($animatedHeadingDescription ?? 'Find something thoughtful for every celebration.');
$animatedHeadingAlign = in_array(($animatedHeadingAlign ?? 'center'), ['left', 'center'], true) ? $animatedHeadingAlign : 'center';
$animatedHeadingTag = in_array(($animatedHeadingTag ?? 'h2'), ['h1', 'h2', 'h3'], true) ? $animatedHeadingTag : 'h2';
$animatedHeadingClass = (string) ($animatedHeadingClass ?? '');
$headingId = 'gift-heading-' . bin2hex(random_bytes(4));
$alignClasses = $animatedHeadingAlign === 'left' ? 'items-start text-left' : 'items-center text-center';
?>
<div id="<?= $headingId ?>" class="gift-animated-heading flex flex-col <?= $alignClasses ?> <?= htmlspecialchars($animatedHeadingClass) ?>">
    <div class="gift-burst relative mb-4 flex h-16 w-20 items-center justify-center" aria-hidden="true">
        <?php
        $particles = [
            ['-25px', '-19px', '#ec4899', '0s'], ['-8px', '-30px', '#f59e0b', '.12s'],
            ['15px', '-28px', '#102E50', '.24s'], ['28px', '-12px', '#10b981', '.36s'],
            ['27px', '11px', '#ec4899', '.48s'], ['-28px', '10px', '#f59e0b', '.6s'],
        ];
        foreach ($particles as [$x, $y, $color, $delay]): ?>
            <span class="gift-particle absolute h-2 w-2 rounded-sm" style="--gift-x:<?= $x ?>;--gift-y:<?= $y ?>;--gift-color:<?= $color ?>;--gift-delay:<?= $delay ?>"></span>
        <?php endforeach; ?>
        <span class="gift-icon relative z-10 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-primary text-white shadow-lg shadow-primary/25">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5v11.25H3.75V9Zm-1.5-4.5h19.5V9H2.25V4.5ZM12 4.5v15.75M12 4.5H8.625a2.625 2.625 0 1 1 0-5.25C11.25-.75 12 4.5 12 4.5Zm0 0h3.375a2.625 2.625 0 1 0 0-5.25C12.75-.75 12 4.5 12 4.5Z"/></svg>
        </span>
    </div>
    <?php if ($animatedHeadingEyebrow !== ''): ?><p class="text-xs font-bold uppercase tracking-[0.24em] text-primary"><?= htmlspecialchars($animatedHeadingEyebrow) ?></p><?php endif; ?>
    <<?= $animatedHeadingTag ?> class="mt-2 text-3xl font-extrabold tracking-tight text-secondary sm:text-4xl"><?= htmlspecialchars($animatedHeadingTitle) ?></<?= $animatedHeadingTag ?>>
    <?php if ($animatedHeadingDescription !== ''): ?><p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base"><?= htmlspecialchars($animatedHeadingDescription) ?></p><?php endif; ?>
</div>

<style>
@keyframes gift-icon-float { 0%,100% { transform: translateY(0) rotate(-3deg); } 50% { transform: translateY(-6px) rotate(3deg); } }
@keyframes gift-spray { 0% { opacity: 0; transform: translate(0,0) scale(.3) rotate(0); } 25% { opacity: 1; } 75% { opacity: .9; } 100% { opacity: 0; transform: translate(var(--gift-x),var(--gift-y)) scale(1) rotate(160deg); } }
#<?= $headingId ?> .gift-icon { animation: gift-icon-float 2.4s ease-in-out infinite; }
#<?= $headingId ?> .gift-particle { background: var(--gift-color); animation: gift-spray 1.8s ease-out var(--gift-delay) infinite; }
@media (prefers-reduced-motion: reduce) { #<?= $headingId ?> .gift-icon, #<?= $headingId ?> .gift-particle { animation: none; } #<?= $headingId ?> .gift-particle { opacity: .75; transform: translate(var(--gift-x),var(--gift-y)); } }
</style>
