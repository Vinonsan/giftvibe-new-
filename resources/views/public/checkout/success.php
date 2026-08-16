<?php
declare(strict_types=1);
$googleReviewUrl = $googleReviewUrl ?? '';
$customer = $_SESSION['user'] ?? null;
// WhatsApp channel url fallback from DB settings if exists
$whatsappUrl = $settings['whatsapp_channel_url'] ?? 'https://whatsapp.com/channel/0029VaEG5zN1yT21oYvI9o2j';
?>
<section class="mx-auto max-w-xl py-12 px-4 text-center space-y-8">
    
    <!-- Success Badge & Confirmation Card -->
    <div class="rounded-3xl border border-primary/10 bg-white p-8 shadow-lg shadow-slate-100/50 space-y-4">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-500/20">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        </div>
        <h1 class="text-2xl font-black text-secondary">Order Received!</h1>
        <p class="text-xs text-slate-500 font-semibold leading-relaxed">
            Order Reference <strong class="text-secondary"><?= htmlspecialchars($orderNumber) ?></strong> is placed. We will verify your deposit receipt shortly and update you via SMS notification.
        </p>
    </div>

    <!-- Google Review Feedback Section -->
    <?php if ($customer && !empty($googleReviewUrl)): ?>
        <div class="rounded-3xl border border-primary/10 bg-white p-8 shadow-lg shadow-slate-100/50 space-y-5">
            <h2 class="text-sm font-black text-secondary uppercase tracking-wider">Rate Your Experience</h2>
            <p class="text-[11px] text-slate-400">Share your thoughts to help us serve you better!</p>

            <!-- Star Rating Row -->
            <div class="flex items-center justify-center gap-2.5">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <button type="button" data-star-rating="<?= $i ?>" class="text-4xl text-slate-200 hover:text-amber-400 focus:outline-none transition-colors cursor-pointer" aria-label="Rate <?= $i ?> stars">★</button>
                <?php endfor; ?>
            </div>
            
            <form id="checkout-google-review-form" class="hidden space-y-4 pt-2">
                <textarea id="checkout-review-text" rows="3" required placeholder="Tell us what you liked about your HAMPER..." class="w-full rounded-2xl border border-primary/10 bg-slate-50 p-3.5 text-xs text-secondary outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition"></textarea>
                
                <button type="button" id="submit-google-review-btn" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-xs font-bold text-white shadow-md shadow-primary/10 hover:bg-secondary transition-all cursor-pointer">
                    <span>Submit Feedback</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </button>
            </form>

            <div id="checkout-review-thanks" class="hidden text-xs font-bold text-emerald-600 bg-emerald-50/50 border border-emerald-100 rounded-xl p-3">
                Thank you! Your feedback has been recorded.
            </div>
        </div>
    <?php endif; ?>

    <!-- WhatsApp Channel Promotion Block -->
    <div class="rounded-3xl border border-emerald-100 bg-emerald-50/30 p-6 shadow-sm flex flex-col sm:flex-row items-center gap-5 text-left relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 h-16 w-16 rounded-full bg-emerald-500/5 group-hover:scale-110 transition-transform"></div>
        
        <div class="h-12 w-12 rounded-2xl bg-emerald-500 p-2.5 shrink-0 flex items-center justify-center shadow-lg shadow-emerald-500/20 text-white">
            <svg class="h-full w-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12.012 2C6.49 2 2.002 6.48 2 12c0 2.202.71 4.248 1.91 5.918L2 22l4.28-.902A9.97 9.97 0 0 0 12.012 23c5.52 0 10.008-4.48 10.008-10s-4.488-10-10.008-10zm0 18.258c-1.996 0-3.954-.532-5.67-1.542l-.406-.24-2.512.53.538-2.45-.262-.416a8.21 8.21 0 0 1-1.26-4.39c.008-4.546 3.702-8.24 8.25-8.24 4.542 0 8.24 3.7 8.24 8.246.002 4.546-3.692 8.242-8.238 8.242z"/></svg>
        </div>
        
        <div class="space-y-1.5">
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Join Our WhatsApp Channel</h3>
            <p class="text-[11px] text-slate-500 leading-normal">Get instant hamper restock alerts, special discounts, and delivery updates.</p>
            <a href="<?= htmlspecialchars($whatsappUrl) ?>" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-[10px] font-bold text-white shadow-sm shadow-emerald-600/10 hover:bg-emerald-700 transition-all cursor-pointer mt-1">
                <span>Follow Channel</span>
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
            </a>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="pt-2">
        <a href="/shop" class="inline-flex rounded-2xl bg-slate-900 px-6 py-3.5 text-xs font-bold text-white hover:bg-rose-500 transition-all shadow-md shadow-slate-900/10 cursor-pointer">Continue Shopping</a>
    </div>

</section>

<?php if ($customer && !empty($googleReviewUrl)): ?>
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

        fetch(<?= json_encode(app_url('/checkout/success/review')) ?>, {
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
                btn.textContent = 'Submit Feedback';
            }
        })
        .catch(function (err) {
            alert('Could not submit feedback. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Submit Feedback';
        });
    });
});
</script>
<?php endif; ?>
