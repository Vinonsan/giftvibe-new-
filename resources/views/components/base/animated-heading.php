<?php

declare(strict_types=1);

$animatedHeadingTitle = (string) ($animatedHeadingTitle ?? 'Gifts made for every moment');
$animatedHeadingEyebrow = (string) ($animatedHeadingEyebrow ?? 'Discover GiftVibe');
$animatedHeadingDescription = (string) ($animatedHeadingDescription ?? 'Find something thoughtful for every celebration.');
$requestedHeadingAlign = (string) ($animatedHeadingAlign ?? 'center');
$animatedHeadingAlign = in_array($requestedHeadingAlign, ['left', 'center'], true) ? $requestedHeadingAlign : 'center';
$requestedHeadingTag = (string) ($animatedHeadingTag ?? 'h2');
$animatedHeadingTag = in_array($requestedHeadingTag, ['h1', 'h2', 'h3'], true) ? $requestedHeadingTag : 'h2';
$animatedHeadingClass = (string) ($animatedHeadingClass ?? '');
$headingId = 'gift-heading-' . bin2hex(random_bytes(4));
$alignClasses = $animatedHeadingAlign === 'left' ? 'items-start text-left' : 'items-center text-center';
?>
<div id="<?= $headingId ?>" class="gift-animated-heading flex flex-col <?= $alignClasses ?> <?= htmlspecialchars($animatedHeadingClass) ?> relative py-4 overflow-hidden">
    <!-- Background glow decoration -->
    <div class="absolute -top-10 left-1/2 -translate-x-1/2 w-48 h-48 bg-primary/5 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
    
    <?php if ($animatedHeadingIcon ?? true): ?>
    <div class="gift-burst relative mb-5 flex h-20 w-24 items-center justify-center" aria-hidden="true">
        <?php
        $particles = [
            ['-28px', '-22px', '#ec4899', '0s'], ['-10px', '-35px', '#f59e0b', '.15s'],
            ['18px', '-32px', '#102E50', '.3s'], ['32px', '-14px', '#10b981', '.45s'],
            ['30px', '14px', '#ec4899', '.6s'], ['-32px', '12px', '#f59e0b', '.75s'],
        ];
        foreach ($particles as [$x, $y, $color, $delay]): ?>
            <span class="gift-particle absolute h-2.5 w-2.5 rounded-full shadow-sm shadow-black/10" style="--gift-x:<?= $x ?>;--gift-y:<?= $y ?>;--gift-color:<?= $color ?>;--gift-delay:<?= $delay ?>"></span>
        <?php endforeach; ?>
        
        <span class="gift-icon relative z-10 inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-gradient-to-br from-[#0B182E] via-secondary to-primary text-white shadow-[0_16px_35px_rgba(11,24,46,.22)] transition-transform duration-300 hover:scale-110">
            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5v11.25H3.75V9Zm-1.5-4.5h19.5V9H2.25V4.5ZM12 4.5v15.75M12 4.5H8.625a2.625 2.625 0 1 1 0-5.25C11.25-.75 12 4.5 12 4.5Zm0 0h3.375a2.625 2.625 0 1 0 0-5.25C12.75-.75 12 4.5 12 4.5Z"/></svg>
        </span>
    </div>
    <?php endif; ?>
    
    <?php if ($animatedHeadingEyebrow !== ''): ?>
        <span class="mb-3 inline-flex items-center gap-2 rounded-full border border-secondary/10 bg-white/80 px-3.5 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.22em] text-secondary shadow-sm backdrop-blur">
            <span class="h-1.5 w-1.5 rounded-full bg-accent animate-pulse"></span>
            <?= htmlspecialchars($animatedHeadingEyebrow) ?>
        </span>
    <?php endif; ?>
    
    <<?= htmlspecialchars($animatedHeadingTag) ?> class="mt-1 bg-gradient-to-r from-[#0B182E] via-secondary to-primary bg-clip-text pb-1 text-3xl font-black tracking-tight text-transparent sm:text-4xl lg:text-5xl">
        <?= htmlspecialchars($animatedHeadingTitle) ?>
    </<?= htmlspecialchars($animatedHeadingTag) ?>>
    
    <?php if ($animatedHeadingDescription !== ''): ?>
        <p class="mt-4 max-w-2xl text-sm leading-relaxed text-slate-500 sm:text-base md:text-lg/7 font-medium px-4">
            <?= htmlspecialchars($animatedHeadingDescription) ?>
        </p>
    <?php endif; ?>
</div>

<style>
@keyframes gift-icon-float { 
    0%, 100% { transform: translateY(0) rotate(-4deg) scale(1); } 
    50% { transform: translateY(-8px) rotate(4deg) scale(1.05); } 
}
@keyframes gift-spray { 
    0% { opacity: 0; transform: translate(0,0) scale(.3) rotate(0); } 
    25% { opacity: 1; } 
    75% { opacity: .9; } 
    100% { opacity: 0; transform: translate(var(--gift-x),var(--gift-y)) scale(1.2) rotate(220deg); } 
}
#<?= $headingId ?> .gift-icon { animation: gift-icon-float 3s ease-in-out infinite; }
#<?= $headingId ?> .gift-particle { background: var(--gift-color); animation: gift-spray 2.2s ease-out var(--gift-delay) infinite; }
@media (prefers-reduced-motion: reduce) { 
    #<?= $headingId ?> .gift-icon, #<?= $headingId ?> .gift-particle { animation: none; } 
    #<?= $headingId ?> .gift-particle { opacity: .75; transform: translate(var(--gift-x),var(--gift-y)); } 
}
</style>
