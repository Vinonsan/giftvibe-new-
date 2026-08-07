<?php
declare(strict_types=1);
$googleReviewUrl = $googleReviewUrl ?? '';
$customer = $_SESSION['user'] ?? null;
?>
<section class="mx-auto max-w-2xl py-12 px-4 text-center">
    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary text-2xl text-white shadow-lg shadow-primary/20">✓</span>
    <h1 class="mt-6 text-3xl font-black text-secondary">Order received</h1>
    <p class="mt-3 text-sm text-secondary/65 leading-relaxed">
        Order <strong class="text-secondary"><?= htmlspecialchars($orderNumber) ?></strong> is waiting for payment verification. You will receive a confirmation or cancellation message after admin review, including the expected delivery date.
    </p>

    <?php if ($customer && !empty($googleReviewUrl)): ?>
        <!-- Google Review & Public Database Testimonial Promotion Section -->
        <div class="mt-10 rounded-3xl border border-primary/10 bg-slate-50/40 p-6 sm:p-8 text-center max-w-lg mx-auto">
            <h2 class="text-lg font-bold text-secondary">Rate Your Experience</h2>
            <p class="mt-1 text-xs text-slate-500">Your feedback helps us make GiftVibe even better!</p>

            <!-- Star Rating -->
            <div class="mt-5 flex items-center justify-center gap-2">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <button type="button" data-star-rating="<?= $i ?>" class="text-3xl text-slate-200 hover:text-amber-400 focus:outline-none transition-colors cursor-pointer" aria-label="Rate <?= $i ?> stars">★</button>
                <?php endfor; ?>
            </div>
            
            <form id="checkout-google-review-form" class="mt-4 hidden space-y-4">
                <textarea id="checkout-review-text" rows="3" required placeholder="Write a brief comment about your experience..." class="w-full rounded-2xl border border-primary/10 bg-white p-3.5 text-xs text-secondary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition"></textarea>
                
                <button type="button" id="submit-google-review-btn" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-xs font-bold text-white shadow-lg shadow-primary/20 hover:bg-secondary transition-all cursor-pointer">
                    <span>Submit & Write a Google Review</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </button>
            </form>

            <div id="checkout-review-thanks" class="mt-4 hidden text-xs font-bold text-emerald-600">
                Thank you! Your feedback has been recorded for admin approval, and we've opened our Google Business page for you.
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var stars = document.querySelectorAll('[data-star-rating]');
            var form = document.getElementById('checkout-google-review-form');
            var btn = document.getElementById('submit-google-review-btn');
            var thanks = document.getElementById('checkout-review-thanks');
            var selectedRating = 5;

            stars.forEach(function (star) {
                star.addEventListener('click', function () {
                    selectedRating = parseInt(star.getAttribute('data-star-rating'), 10);
                    updateStars(selectedRating);
                    form.classList.remove('hidden');
                });
                star.addEventListener('mouseover', function () {
                    var hoverVal = parseInt(star.getAttribute('data-star-rating'), 10);
                    updateStars(hoverVal);
                });
                star.addEventListener('mouseout', function () {
                    updateStars(selectedRating);
                });
            });

            function updateStars(rating) {
                stars.forEach(function (s, index) {
                    if (index < rating) {
                        s.classList.add('text-amber-400');
                        s.classList.remove('text-slate-200');
                    } else {
                        s.classList.remove('text-amber-400');
                        s.classList.add('text-slate-200');
                    }
                });
            }

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var commentText = document.getElementById('checkout-review-text').value;
                if (!commentText.trim()) {
                    alert('Please write a brief comment.');
                    return;
                }

                btn.disabled = true;
                btn.textContent = 'Submitting...';

                var formData = new FormData();
                formData.append('rating', selectedRating);
                formData.append('comments', commentText);

                fetch('/checkout/success/review', {
                    method: 'POST',
                    body: formData
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        form.classList.add('hidden');
                        thanks.classList.remove('hidden');
                        if (data.google_review_url) {
                            window.open(data.google_review_url, '_blank');
                        }
                    } else {
                        alert(data.message || 'Something went wrong.');
                        btn.disabled = false;
                        btn.textContent = 'Submit & Write a Google Review';
                    }
                })
                .catch(function (err) {
                    alert('Could not submit feedback. Please try again.');
                    btn.disabled = false;
                    btn.textContent = 'Submit & Write a Google Review';
                });
            });
        });
        </script>
    <?php endif; ?>

    <div class="mt-8">
        <a href="/shop" class="inline-flex rounded-xl bg-primary px-6 py-3 text-sm font-bold text-white hover:bg-secondary transition-all">Continue shopping</a>
    </div>
</section>
