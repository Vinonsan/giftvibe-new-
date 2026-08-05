<?php 
declare(strict_types=1);
$faqs = $faqs ?? [];
if (!$faqs) return;
?>
<section class="relative overflow-hidden bg-slate-50/20 py-16 sm:py-24" aria-labelledby="faq-title">
    <!-- Subtle premium radial background glow -->
    <div class="pointer-events-none absolute left-1/2 top-0 h-[350px] w-[500px] -translate-x-1/2 -z-10 rounded-full bg-gradient-to-b from-primary/5 to-transparent blur-3xl" aria-hidden="true"></div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Clean, Premium Center Header (No cluttered lines or side icons) -->
        <div class="text-center mb-16">
            <!-- Modern category badge with a gradient micro-glow -->
            <span class="inline-flex items-center rounded-full bg-white px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-primary shadow-sm border border-slate-100/80">
                <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-primary animate-pulse"></span>
                Helpful Answers
            </span>
            
            <h2 id="faq-title" class="mt-5 text-3xl font-extrabold tracking-tight text-secondary sm:text-4xl">
                Frequently Asked Questions
            </h2>
        </div>

        <!-- Full-Width FAQ Accordion Grid-1 (iOS-style premium design) -->
        <div class="space-y-4">
            <?php foreach($faqs as $i => $faq): ?>
                <details class="group rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-200/80" <?= $i === 0 ? 'open' : '' ?>>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-bold text-secondary outline-none select-none">
                        <span class="text-sm sm:text-base tracking-tight transition-colors duration-200 group-hover:text-primary"><?= htmlspecialchars($faq['question']) ?></span>
                        
                        <!-- Modern Plus-to-Minus CSS Animated Toggle Indicator -->
                        <span class="relative flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-50 text-secondary transition-all duration-300 group-open:bg-primary group-open:text-white">
                            <!-- Horizontal bar -->
                            <span class="absolute h-0.5 w-2.5 bg-current transition-transform duration-300"></span>
                            <!-- Vertical bar (scales to 0 when open) -->
                            <span class="absolute h-2.5 w-0.5 bg-current transition-all duration-300 group-open:scale-y-0 group-open:opacity-0"></span>
                        </span>
                    </summary>
                    <div class="mt-3 text-xs sm:text-sm leading-relaxed text-slate-500 border-t border-slate-50/50 pt-3">
                        <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
