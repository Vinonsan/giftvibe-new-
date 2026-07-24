<div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    <!-- Breadcrumbs -->
    <?php component('public/components/common/breadcrumbs', ['items' => [['label' => 'Showcase']]]); ?>

    <!-- Section Heading -->
    <?php component('public/components/common/section-heading', [
        'title' => 'Public UI Component Showcase',
        'subtitle' => 'Manually built reusable component system using Tailwind CSS.'
    ]); ?>

    <!-- 1. Buttons Showcase -->
    <section class="bg-white border border-slate-200 rounded-card shadow-sm p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">1. Reusable Buttons</h3>
        <div class="flex flex-wrap items-center gap-3">
            <?php component('public/components/common/button', ['label' => 'Primary Button', 'variant' => 'primary']); ?>
            <?php component('public/components/common/button', ['label' => 'Secondary Button', 'variant' => 'secondary']); ?>
            <?php component('public/components/common/button', ['label' => 'Accent Button', 'variant' => 'accent']); ?>
            <?php component('public/components/common/button', ['label' => 'Outline Button', 'variant' => 'outline']); ?>
            <?php component('public/components/common/button', ['label' => 'Ghost Button', 'variant' => 'ghost']); ?>
            <?php component('public/components/common/button', ['label' => 'Danger Button', 'variant' => 'danger']); ?>
            <?php component('public/components/common/button', ['label' => 'Success Button', 'variant' => 'success']); ?>
            <?php component('public/components/common/button', ['label' => 'Link Button', 'variant' => 'link']); ?>
        </div>
        <div class="flex flex-wrap items-center gap-3 pt-2">
            <?php component('public/components/common/button', ['label' => 'Size XS', 'size' => 'xs']); ?>
            <?php component('public/components/common/button', ['label' => 'Size SM', 'size' => 'sm']); ?>
            <?php component('public/components/common/button', ['label' => 'Size MD', 'size' => 'md']); ?>
            <?php component('public/components/common/button', ['label' => 'Size LG', 'size' => 'lg']); ?>
            <?php component('public/components/common/button', ['label' => 'Size XL', 'size' => 'xl']); ?>
        </div>
        <div class="flex flex-wrap items-center gap-3 pt-2">
            <?php component('public/components/common/button', ['label' => 'With Left Icon', 'leftIcon' => 'gift']); ?>
            <?php component('public/components/common/button', ['label' => 'With Right Icon', 'rightIcon' => 'arrow-right']); ?>
            <?php component('public/components/common/button', ['label' => 'Loading State', 'loading' => true]); ?>
            <?php component('public/components/common/button', ['label' => 'Disabled State', 'disabled' => true]); ?>
        </div>
    </section>

    <!-- 2. Badges & Alerts -->
    <section class="bg-white border border-slate-200 rounded-card shadow-sm p-6 space-y-6">
        <div>
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">2. Badges</h3>
            <div class="flex flex-wrap gap-2">
                <?php component('public/components/common/badge', ['label' => 'Primary Badge', 'variant' => 'primary']); ?>
                <?php component('public/components/common/badge', ['label' => 'Secondary Badge', 'variant' => 'secondary']); ?>
                <?php component('public/components/common/badge', ['label' => 'Success Badge', 'variant' => 'success']); ?>
                <?php component('public/components/common/badge', ['label' => 'Warning Badge', 'variant' => 'warning']); ?>
                <?php component('public/components/common/badge', ['label' => 'Danger Badge', 'variant' => 'danger']); ?>
                <?php component('public/components/common/badge', ['label' => 'Info Badge', 'variant' => 'info']); ?>
                <?php component('public/components/common/badge', ['label' => 'Gray Badge', 'variant' => 'gray']); ?>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">3. Alerts</h3>
            <div class="space-y-3">
                <?php component('public/components/common/alert', ['message' => 'Your order has been completed successfully!', 'type' => 'success', 'dismissible' => true]); ?>
                <?php component('public/components/common/alert', ['message' => 'Please confirm your address details before paying.', 'type' => 'warning', 'title' => 'Attention Needed']); ?>
            </div>
        </div>
    </section>

    <!-- 3. Form Inputs -->
    <section class="bg-white border border-slate-200 rounded-card shadow-sm p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">4. Reusable Form Fields</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php component('public/components/common/form-field', [
                'label' => 'Full Name',
                'name' => 'fullname',
                'id' => 'fullname',
                'placeholder' => 'John Doe',
                'required' => true
            ]); ?>
            <?php component('public/components/common/form-field', [
                'label' => 'Email Address',
                'name' => 'email',
                'id' => 'email',
                'type' => 'email',
                'errorText' => 'Please enter a valid email address.'
            ]); ?>
            <?php component('public/components/common/select-field', [
                'label' => 'Delivery Location',
                'name' => 'location',
                'id' => 'location',
                'options' => [
                    ['value' => 'colombo', 'label' => 'Colombo'],
                    ['value' => 'kandy', 'label' => 'Kandy'],
                    ['value' => 'galle', 'label' => 'Galle'],
                ]
            ]); ?>
            <?php component('public/components/common/file-field', [
                'label' => 'Custom Gift Card Image',
                'name' => 'giftcard_img',
                'id' => 'giftcard_img',
                'helpText' => 'Upload a JPEG or PNG file (Max 2MB).'
            ]); ?>
            <div class="md:col-span-2">
                <?php component('public/components/common/textarea-field', [
                    'label' => 'Gift Message / Notes',
                    'name' => 'notes',
                    'id' => 'notes',
                    'placeholder' => 'Write your custom gift message here...'
                ]); ?>
            </div>
            <div class="space-y-2">
                <?php component('public/components/common/checkbox-field', [
                    'label' => 'Subscribe to weekly gift ideas',
                    'name' => 'newsletter',
                    'id' => 'newsletter',
                    'checked' => true
                ]); ?>
            </div>
            <div class="space-y-2">
                <?php component('public/components/common/radio-field', [
                    'label' => 'Wrap as a premium birthday gift (LKR 500)',
                    'name' => 'wrapping',
                    'id' => 'wrapping_bday',
                    'value' => 'birthday',
                    'selectedValue' => 'birthday'
                ]); ?>
                <?php component('public/components/common/radio-field', [
                    'label' => 'No special wrapping',
                    'name' => 'wrapping',
                    'id' => 'wrapping_none',
                    'value' => 'none',
                    'selectedValue' => 'birthday'
                ]); ?>
            </div>
        </div>
    </section>

    <!-- 4. Interactive Components (Tabs, Accordion, Modal) -->
    <section class="bg-white border border-slate-200 rounded-card shadow-sm p-6 space-y-6">
        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">5. Interactive Elements</h3>

        <!-- Tabs -->
        <div>
            <h4 class="text-sm font-bold text-slate-700 mb-3">Tabs Component</h4>
            <?php component('public/components/common/tabs', [
                'tabs' => [
                    ['id' => 'tab-description', 'label' => 'Description', 'content' => '<p class="text-slate-650">This is a premium handcrafted chocolate gift box made from organic cocoa.</p>', 'active' => true],
                    ['id' => 'tab-specifications', 'label' => 'Specifications', 'content' => '<ul class="list-disc pl-5"><li>Weight: 250g</li><li>16 Chocolate Pieces</li></ul>']
                ]
            ]); ?>
        </div>

        <!-- Accordions -->
        <div class="pt-4">
            <h4 class="text-sm font-bold text-slate-700 mb-3">Accordions / Collapsible FAQ</h4>
            <?php component('public/components/common/accordion', [
                'items' => [
                    ['title' => 'Do you support same-day delivery?', 'content' => 'Yes, same-day delivery is available in Colombo for orders placed before 12 PM.'],
                    ['title' => 'What payment methods are supported?', 'content' => 'We support Visa, Mastercard, AMEX, and Bank Transfers.']
                ]
            ]); ?>
        </div>

        <!-- Modals & Drawers Trigger -->
        <div class="pt-4 space-y-3">
            <h4 class="text-sm font-bold text-slate-700 mb-3">Modals & Drawers Trigger</h4>
            <div class="flex gap-3">
                <?php component('public/components/common/button', [
                    'label' => 'Open Sample Modal',
                    'variant' => 'primary',
                    'size' => 'sm',
                    'type' => 'button',
                    'rightIcon' => 'eye'
                ]); ?>
                <script>
                    document.currentScript.previousElementSibling.addEventListener('click', () => openModal('#test-modal'));
                </script>

                <?php component('public/components/common/button', [
                    'label' => 'Toggle Drawer',
                    'variant' => 'outline',
                    'size' => 'sm',
                    'type' => 'button',
                    'rightIcon' => 'chevron-right'
                ]); ?>
                <script>
                    document.currentScript.previousElementSibling.addEventListener('click', () => toggleDrawer('#test-drawer'));
                </script>
            </div>
        </div>
    </section>

    <!-- 5. Cards & Showcase Grid -->
    <section class="space-y-6">
        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4 bg-white p-4 rounded border border-slate-200 shadow-sm">6. Product & Grid Cards</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Product Card -->
            <?php component('public/components/common/product-card', [
                'name' => 'Premium Chocolate Gift Box',
                'url' => '#',
                'image' => 'https://placehold.co/320x320/png',
                'price' => 4500.00,
                'oldPrice' => 5200.00,
                'category' => 'Chocolates',
                'discountBadge' => '15% OFF',
                'rating' => 4.5,
                'reviewCount' => 24,
                'featured' => true
            ]); ?>

            <!-- Category Card -->
            <?php component('public/components/common/category-card', [
                'name' => 'Flowers & Bouquets',
                'url' => '#',
                'image' => 'https://placehold.co/400x300/png',
                'itemCount' => 112,
                'description' => 'Fresh flowers delivered'
            ]); ?>

            <!-- Blog Card -->
            <?php component('public/components/common/blog-card', [
                'title' => 'Top 10 Romantic Birthday Gift Ideas for Partners',
                'url' => '#',
                'image' => 'https://placehold.co/600x400/png',
                'excerpt' => 'Finding the perfect gift can be challenging. Here is a curated list of romantic items that will delight them.',
                'author' => 'Imal Silva',
                'date' => 'July 24, 2026',
                'category' => 'Gifting Guide',
                'readingTime' => '4'
            ]); ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pt-4">
            <!-- Occasion Card -->
            <?php component('public/components/common/occasion-card', [
                'title' => 'Anniversary',
                'url' => '#',
                'icon' => 'heart',
                'description' => 'Show your endless love'
            ]); ?>

            <!-- Recipient Card -->
            <?php component('public/components/common/recipient-card', [
                'title' => 'For Her',
                'url' => '#',
                'icon' => 'user',
                'description' => 'Gifts she will cherish'
            ]); ?>

            <!-- FAQ Item Accordion Style -->
            <div class="col-span-2 bg-white border border-slate-200 rounded-card p-6">
                <h4 class="text-sm font-bold text-slate-800 mb-4">FAQ Item Accordion</h4>
                <?php component('public/components/common/faq-item', [
                    'id' => 'faq-item-1',
                    'question' => 'How can I trace my order status?',
                    'answer' => 'You will receive a real-time order tracking URL on SMS/WhatsApp once the order has been processed.'
                ]); ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
            <!-- Testimonial Card -->
            <?php component('public/components/common/testimonial-card', [
                'name' => 'Fathima Rizna',
                'text' => 'The chocolates were fresh and delivery was right on time. Highly recommended!',
                'rating' => 5,
                'location' => 'Dehiwala'
            ]); ?>

            <!-- Empty State -->
            <div class="col-span-2">
                <?php component('public/components/common/empty-state', [
                    'title' => 'No products match filters',
                    'description' => 'Try expanding your search query or removing filters to discover more premium items.',
                    'cta' => ['label' => 'Reset All Filters', 'href' => '#']
                ]); ?>
            </div>
        </div>
    </section>

    <!-- Modal Element definition -->
    <?php component('public/components/common/modal', [
        'id' => 'test-modal',
        'title' => 'UI Showcase Dialog',
        'content' => '<p>This is a modal popup window generated safely inside the server-side component engine.</p>',
        'footer' => '
            <button type="button" class="px-4 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 text-sm font-semibold rounded-button" onclick="closeModal(\'#test-modal\')">Close</button>
            <button type="button" class="px-4 py-2 bg-primary hover:bg-primary-600 text-white text-sm font-semibold rounded-button" onclick="alert(\'Proceed action clicked!\')">Save</button>
        '
    ]); ?>

    <!-- Drawer Element definition -->
    <?php component('public/components/common/drawer', [
        'id' => 'test-drawer',
        'title' => 'Customer Cart Preview',
        'content' => '
            <div class="space-y-4">
                <p>Your shopping basket is empty. Add premium gift packs to unlock discount rewards!</p>
                <button type="button" class="w-full bg-primary hover:bg-primary-600 text-white font-bold py-2 rounded-button" onclick="toggleDrawer(\'#test-drawer\')">Continue Shopping</button>
            </div>
        '
    ]); ?>
</div>
