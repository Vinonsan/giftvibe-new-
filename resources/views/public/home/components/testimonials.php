<?php

declare(strict_types=1);

$testimonials = array_slice($testimonials ?? [], 0, 6);
if (!$testimonials) return;

$initials = static function (string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $value = '';
    foreach (array_slice($parts, 0, 2) as $part) $value .= mb_strtoupper(mb_substr($part, 0, 1));
    return $value ?: 'G';
};

$positions = [
    ['left' => 9,  'top' => 30],
    ['left' => 28, 'top' => 18],
    ['left' => 72, 'top' => 18],
    ['left' => 91, 'top' => 30],
    ['left' => 17, 'top' => 77],
    ['left' => 83, 'top' => 77],
];
?>

<section id="testimonials" class="relative overflow-hidden bg-white py-20 sm:py-28" aria-labelledby="testimonial-title" data-testimonial-constellation>
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute left-1/2 top-1/2 h-[520px] w-[520px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-100/60 blur-[100px]"></div>
        <div class="absolute -left-32 bottom-0 h-72 w-72 rounded-full bg-rose-100/40 blur-[100px]"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2 text-[10px] font-extrabold uppercase tracking-[.22em] text-primary">
                <span class="h-1.5 w-1.5 rounded-full bg-primary shadow-[0_0_10px_rgba(59,130,246,.8)]"></span>
                Shared with love
            </span>
            <h2 id="testimonial-title" class="mt-5 text-4xl font-black tracking-[-.04em] text-secondary sm:text-5xl">Happy moments, delivered.</h2>
            <p class="mx-auto mt-4 max-w-lg text-sm leading-7 text-slate-500">Tap a customer to discover the moments GiftVibe helped make memorable.</p>
        </div>

        <div class="relative mx-auto mt-16 hidden h-[590px] max-w-6xl sm:block" data-constellation-stage>
            <svg class="pointer-events-none absolute inset-0 h-full w-full" viewBox="0 0 1100 590" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="testimonial-line" x1="0" y1="0" x2="1" y2="1">
                        <stop stop-color="#93c5fd" stop-opacity=".25"/>
                        <stop offset=".5" stop-color="#3b82f6" stop-opacity=".7"/>
                        <stop offset="1" stop-color="#fda4af" stop-opacity=".25"/>
                    </linearGradient>
                </defs>
                <path d="M105 177 Q310 190 550 295"/><path d="M308 106 Q410 170 550 295"/>
                <path d="M792 106 Q690 170 550 295"/><path d="M995 177 Q790 190 550 295"/>
                <path d="M187 455 Q360 430 550 295"/><path d="M913 455 Q740 430 550 295"/>
                <style>path{fill:none;stroke:url(#testimonial-line);stroke-width:1.5;stroke-dasharray:5 9;stroke-linecap:round}</style>
            </svg>

            <div class="pointer-events-none absolute left-1/2 top-1/2 h-[390px] w-[390px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-blue-100/80"></div>
            <div class="pointer-events-none absolute left-1/2 top-1/2 h-[490px] w-[490px] -translate-x-1/2 -translate-y-1/2 rounded-full border border-dashed border-blue-100/60"></div>

            <?php foreach ($testimonials as $index => $review):
                $payload = htmlspecialchars(json_encode([
                    'name' => $review['reviewer_name'],
                    'role' => $review['reviewer_role'] ?: 'GiftVibe customer',
                    'title' => $review['title'] ?: 'A memorable GiftVibe experience',
                    'text' => $review['review_text'],
                    'rating' => (int) $review['rating'],
                    'avatar' => $review['avatar_path'] ?: '',
                    'initials' => $initials((string) $review['reviewer_name']),
                ], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES);
                $position = $positions[$index];
            ?>
                <button type="button" data-person data-review="<?= $payload ?>" data-active="<?= $index === 0 ? 'true' : 'false' ?>" aria-label="Read <?= htmlspecialchars((string) $review['reviewer_name']) ?>'s review" class="testimonial-person absolute z-20 -translate-x-1/2 -translate-y-1/2 text-center" style="left:<?= $position['left'] ?>%;top:<?= $position['top'] ?>%;--delay:<?= $index * .45 ?>s">
                    <span class="relative mx-auto block h-[76px] w-[76px] rounded-full bg-white p-1.5 shadow-[0_15px_38px_rgba(15,45,80,.18)] ring-1 ring-slate-100 transition duration-300 group-hover:scale-105 data-[active=true]:ring-4 data-[active=true]:ring-blue-400/25">
                        <?php if (!empty($review['avatar_path'])): ?>
                            <img src="<?= htmlspecialchars((string) $review['avatar_path']) ?>" alt="" class="h-full w-full rounded-full object-cover">
                        <?php else: ?>
                            <span class="flex h-full w-full items-center justify-center rounded-full bg-gradient-to-br from-secondary to-primary text-sm font-black text-white"><?= htmlspecialchars($initials((string) $review['reviewer_name'])) ?></span>
                        <?php endif; ?>
                        <span class="absolute bottom-0 right-0 h-4 w-4 rounded-full border-[3px] border-white bg-emerald-400"></span>
                    </span>
                    <span class="mt-3 block rounded-full border border-slate-100 bg-white/90 px-3 py-1.5 text-xs font-extrabold text-secondary shadow-sm backdrop-blur transition data-[active=true]:border-blue-200 data-[active=true]:bg-primary data-[active=true]:text-white"><?= htmlspecialchars((string) $review['reviewer_name']) ?></span>
                </button>
            <?php endforeach; ?>

            <article class="absolute left-1/2 top-1/2 z-10 flex h-[370px] w-[370px] -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-center rounded-full border-[8px] border-white bg-white/90 p-11 text-center shadow-[0_30px_80px_rgba(19,58,99,.16)] ring-1 ring-blue-100 backdrop-blur-xl lg:h-[390px] lg:w-[390px]" data-review-panel aria-live="polite">
                <span class="absolute -top-5 grid h-11 w-11 place-items-center rounded-full bg-primary font-serif text-3xl text-white shadow-lg shadow-blue-500/25" aria-hidden="true">&ldquo;</span>
                <div class="text-sm tracking-[.2em] text-amber-400" data-rating><?= str_repeat('&#9733;', (int) $testimonials[0]['rating']) ?></div>
                <h3 class="mt-4 text-xl font-black tracking-tight text-secondary" data-title><?= htmlspecialchars((string) ($testimonials[0]['title'] ?: 'A memorable GiftVibe experience')) ?></h3>
                <blockquote class="mt-4 line-clamp-4 text-sm leading-7 text-slate-600" data-text>&ldquo;<?= htmlspecialchars((string) $testimonials[0]['review_text']) ?>&rdquo;</blockquote>
                <div class="mt-5 h-px w-10 bg-blue-200"></div>
                <p class="mt-3 text-sm font-black text-secondary" data-name><?= htmlspecialchars((string) $testimonials[0]['reviewer_name']) ?></p>
                <p class="mt-1 text-xs text-slate-400" data-role><?= htmlspecialchars((string) ($testimonials[0]['reviewer_role'] ?: 'GiftVibe customer')) ?></p>
            </article>
        </div>

        <div class="mt-10 sm:hidden">
            <div class="flex gap-3 overflow-x-auto px-1 pb-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <?php foreach ($testimonials as $index => $review):
                    $payload = htmlspecialchars(json_encode(['name'=>$review['reviewer_name'],'role'=>$review['reviewer_role']?:'GiftVibe customer','title'=>$review['title']?:'A memorable GiftVibe experience','text'=>$review['review_text'],'rating'=>(int)$review['rating']], JSON_HEX_APOS|JSON_HEX_QUOT), ENT_QUOTES);
                ?>
                    <button type="button" data-mobile-person data-review="<?= $payload ?>" data-active="<?= $index === 0 ? 'true' : 'false' ?>" class="shrink-0 rounded-2xl border border-slate-100 bg-white p-2 shadow-sm data-[active=true]:border-blue-300 data-[active=true]:bg-blue-50">
                        <span class="block h-14 w-14 overflow-hidden rounded-xl">
                            <?php if (!empty($review['avatar_path'])): ?><img src="<?= htmlspecialchars((string)$review['avatar_path']) ?>" alt="" class="h-full w-full object-cover"><?php else: ?><span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-secondary to-primary text-xs font-black text-white"><?= htmlspecialchars($initials((string)$review['reviewer_name'])) ?></span><?php endif; ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
            <article class="mt-3 rounded-3xl border border-blue-100 bg-white p-7 text-center shadow-[0_20px_55px_rgba(19,58,99,.12)]" data-mobile-panel aria-live="polite">
                <div class="text-sm tracking-[.18em] text-amber-400" data-rating><?= str_repeat('&#9733;', (int)$testimonials[0]['rating']) ?></div>
                <h3 class="mt-4 text-xl font-black text-secondary" data-title><?= htmlspecialchars((string)($testimonials[0]['title']?:'A memorable GiftVibe experience')) ?></h3>
                <blockquote class="mt-4 text-sm leading-7 text-slate-600" data-text>&ldquo;<?= htmlspecialchars((string)$testimonials[0]['review_text']) ?>&rdquo;</blockquote>
                <p class="mt-5 text-sm font-black text-secondary" data-name><?= htmlspecialchars((string)$testimonials[0]['reviewer_name']) ?></p>
                <p class="mt-1 text-xs text-slate-400" data-role><?= htmlspecialchars((string)($testimonials[0]['reviewer_role']?:'GiftVibe customer')) ?></p>
            </article>
        </div>
    </div>
</section>

<style>
@keyframes testimonial-float{0%,100%{transform:translate(-50%,-50%) translateY(0)}50%{transform:translate(-50%,-50%) translateY(-10px)}}
.testimonial-person{animation:testimonial-float 5s ease-in-out var(--delay) infinite}
.testimonial-person[data-active="true"]>span:first-child{box-shadow:0 18px 45px rgba(37,99,235,.25);outline:4px solid rgba(96,165,250,.18)}
.testimonial-person[data-active="true"]>span:last-child{border-color:#bfdbfe;background:#2563eb;color:#fff}
@media(prefers-reduced-motion:reduce){.testimonial-person{animation:none}}
</style>

<script>
(() => {
    const root = document.querySelector('[data-testimonial-constellation]');
    if (!root) return;
    const bind = (buttons, panel) => {
        if (!panel) return;
        const fields = {rating:panel.querySelector('[data-rating]'),title:panel.querySelector('[data-title]'),text:panel.querySelector('[data-text]'),name:panel.querySelector('[data-name]'),role:panel.querySelector('[data-role]')};
        buttons.forEach(button => button.addEventListener('click', () => {
            const review = JSON.parse(button.dataset.review);
            buttons.forEach(item => item.dataset.active = item === button ? 'true' : 'false');
            panel.animate([{opacity:.55,transform:'translate(-50%,-48%) scale(.98)'},{opacity:1,transform:'translate(-50%,-50%) scale(1)'}],{duration:350,easing:'ease-out'});
            fields.rating.innerHTML='&#9733;'.repeat(review.rating);fields.title.textContent=review.title;fields.text.textContent='\u201c'+review.text+'\u201d';fields.name.textContent=review.name;fields.role.textContent=review.role;
        }));
    };
    bind([...root.querySelectorAll('[data-person]')], root.querySelector('[data-review-panel]'));
    const mobileButtons=[...root.querySelectorAll('[data-mobile-person]')],mobilePanel=root.querySelector('[data-mobile-panel]');
    if(mobilePanel){mobileButtons.forEach(button=>button.addEventListener('click',()=>{const review=JSON.parse(button.dataset.review);mobileButtons.forEach(item=>item.dataset.active=item===button?'true':'false');mobilePanel.animate([{opacity:.6,transform:'translateY(6px)'},{opacity:1,transform:'translateY(0)'}],{duration:300,easing:'ease-out'});mobilePanel.querySelector('[data-rating]').innerHTML='&#9733;'.repeat(review.rating);mobilePanel.querySelector('[data-title]').textContent=review.title;mobilePanel.querySelector('[data-text]').textContent='\u201c'+review.text+'\u201d';mobilePanel.querySelector('[data-name]').textContent=review.name;mobilePanel.querySelector('[data-role]').textContent=review.role}))}
})();
</script>
