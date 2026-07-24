<?php
/**
 * Variables:
 * @var string $title
 */
?>
<div class="bg-white border border-slate-200 rounded-card shadow-card p-6">
    <h3 class="text-xs font-bold text-slate-850 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2"><?= e($title) ?></h3>
    <!-- Chart Placeholder Graphic (pure Tailwind SVG mockup) -->
    <div class="h-64 bg-slate-50 border border-dashed border-slate-200 rounded flex flex-col items-center justify-center text-slate-450 p-4">
        <svg class="w-16 h-16 opacity-30 text-primary" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="10" y="70" width="15" height="20" rx="3" fill="currentColor"/>
            <rect x="35" y="40" width="15" height="50" rx="3" fill="currentColor"/>
            <rect x="60" y="20" width="15" height="70" rx="3" fill="currentColor"/>
            <rect x="85" y="55" width="15" height="35" rx="3" fill="currentColor"/>
        </svg>
        <span class="text-xs font-semibold mt-2">Analytical visualization mock chart</span>
    </div>
</div>