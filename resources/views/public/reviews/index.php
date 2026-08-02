<?php
declare(strict_types=1);

$reviews = $reviews ?? [];
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';

$fieldClass = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition';
?>
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    
    <!-- Header Component -->
    <?php
    $animatedHeadingEyebrow = 'TESTIMONIALS';
    $animatedHeadingTitle = 'Client Stories';
    $animatedHeadingDescription = 'Real experiences shared by our valued customers across Sri Lanka.';
    require BASE_PATH . '/resources/views/components/base/animated-heading.php';
    ?>

    <?php if ($flash): ?>
        <div class="mx-auto max-w-3xl mt-6 rounded-xl px-4 py-3.5 text-sm font-semibold text-center <?= $flash['type'] === 'error' ? 'bg-rose-50 text-rose-700 border border-rose-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="mt-12 grid grid-cols-1 gap-12 lg:grid-cols-12">
        <!-- Left Column: Submit a Review (4 cols on lg) -->
        <div class="lg:col-span-4 bg-slate-50/50 rounded-3xl border border-slate-100 p-6 lg:p-8 self-start">
            <h3 class="text-lg font-bold text-secondary tracking-tight">Share Your Experience</h3>
            <p class="mt-1.5 text-xs text-slate-500 leading-normal">Your feedback helps us grow and keep spreading positive vibes.</p>
            
            <form id="public-review-form" method="post" action="/reviews" enctype="multipart/form-data" class="mt-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <!-- Honeypot -->
                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-secondary">Your Name *</label>
                    <input type="text" required name="reviewer_name" placeholder="e.g. யாழினி" class="<?= $fieldClass ?>">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-secondary">Your Email *</label>
                    <input type="email" required name="reviewer_email" placeholder="you@example.com" class="<?= $fieldClass ?>">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-secondary">Location / Role</label>
                    <input type="text" name="reviewer_role" placeholder="e.g. யாழ்ப்பாணம், இலங்கை" class="<?= $fieldClass ?>">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-secondary">Profile Photo (Optional)</label>
                    <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/5 file:text-primary hover:file:bg-primary/10 file:cursor-pointer">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-secondary">Review Title</label>
                    <input type="text" name="title" placeholder="e.g. சிறந்த சேவை!" class="<?= $fieldClass ?>">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-secondary">Rating *</label>
                    <?php
                    $selectName = 'rating';
                    $selectId = 'review-rating';
                    $selectOptions = ['5' => '5 Stars', '4' => '4 Stars', '3' => '3 Stars', '2' => '2 Stars', '1' => '1 Star'];
                    $selectValue = '5';
                    $selectLabel = '';
                    $selectRequired = true;
                    require BASE_PATH . '/resources/views/components/base/select.php';
                    ?>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-secondary">Your Review *</label>
                    <textarea required name="review_text" rows="4" placeholder="Tell us what you liked about your GiftVibe experience (minimum 20 characters)..." class="<?= $fieldClass ?>"></textarea>
                </div>

                <div class="pt-2">
                    <?php
                    $buttonLabel = 'Submit Review';
                    $buttonType = 'submit';
                    $buttonClass = 'w-full rounded-xl py-3 text-xs font-bold shadow-md shadow-primary/10 hover:shadow-lg transition active:scale-98';
                    require BASE_PATH . '/resources/views/components/base/button.php';
                    ?>
                </div>
            </form>
        </div>

        <!-- Right Column: Reviews Grid (8 cols on lg) -->
        <div class="lg:col-span-8 space-y-6">
            <h3 class="text-xl font-extrabold text-secondary tracking-tight">Approved Reviews (<?= count($reviews) ?>)</h3>
            
            <?php if (!$reviews): ?>
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-16 text-center text-sm text-slate-500">
                    No reviews have been published yet. Be the first to share your experience!
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($reviews as $review): 
                        $avatar = (string)($review['avatar_path'] ?? '');
                        if (str_starts_with($avatar, 'public/')) $avatar = '/' . substr($avatar, 7);
                        $ratingVal = (int)($review['rating'] ?? 5);
                    ?>
                        <article class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition hover:shadow-md">
                            <div>
                                <!-- Rating Stars -->
                                <div class="flex items-center gap-0.5 text-sm text-amber-400">
                                    <?= str_repeat('★', $ratingVal) ?><?= str_repeat('☆', 5 - $ratingVal) ?>
                                </div>

                                <!-- Review Title -->
                                <h4 class="mt-3 text-sm font-bold text-secondary">
                                    <?= htmlspecialchars($review['title'] ?: 'A memorable GiftVibe experience') ?>
                                </h4>

                                <!-- Review Text -->
                                <blockquote class="mt-2 text-xs leading-relaxed text-slate-600">
                                    “<?= htmlspecialchars($review['review_text']) ?>”
                                </blockquote>
                            </div>

                            <!-- Reviewer Profile Info -->
                            <div class="mt-6 flex items-center gap-3 border-t border-slate-50 pt-4">
                                <?php if ($avatar !== ''): ?>
                                    <img src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($review['reviewer_name']) ?>" class="h-9 w-9 rounded-full object-cover">
                                <?php else: ?>
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/5 text-xs font-bold text-primary">
                                        <?= htmlspecialchars(mb_strtoupper(mb_substr($review['reviewer_name'], 0, 1))) ?>
                                    </span>
                                <?php endif; ?>
                                <div>
                                    <p class="text-xs font-bold text-secondary"><?= htmlspecialchars($review['reviewer_name']) ?></p>
                                    <?php if ($review['reviewer_role']): ?>
                                        <p class="text-[10px] font-medium text-slate-400"><?= htmlspecialchars($review['reviewer_role']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
