<div class="space-y-8">
    <!-- Page Header -->
    <?php component('admin/components/common/page-header', [
        'title' => 'Admin UI Showcase',
        'description' => 'Preview dashboard statistics, tables, form controls, and messaging modals.',
        'actions' => '
            <button type="button" class="px-4 py-2 bg-primary hover:bg-primary-600 text-white text-sm font-semibold rounded-button shadow-sm" onclick="openModal(\'#admin-showcase-modal\')">Open Dialog</button>
            <button type="button" class="px-4 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 text-sm font-semibold rounded-button">Export PDF</button>
        '
    ]); ?>

    <!-- 1. Stats Row -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <?php component('admin/components/dashboard/stat-card', [
            'title' => 'Total Orders',
            'value' => '1,248',
            'change' => '+14.2%',
            'icon' => 'shopping-bag'
        ]); ?>
        <?php component('admin/components/dashboard/stat-card', [
            'title' => 'Gross Revenue',
            'value' => 'LKR 845,000',
            'change' => '+8.5%',
            'icon' => 'dashboard'
        ]); ?>
        <?php component('admin/components/dashboard/stat-card', [
            'title' => 'Staff Users',
            'value' => '12',
            'change' => '0%',
            'icon' => 'users'
        ]); ?>
        <?php component('admin/components/dashboard/stat-card', [
            'title' => 'Pending Support',
            'value' => '4',
            'change' => '-5%',
            'icon' => 'warning'
        ]); ?>
    </div>

    <!-- 2. Data Table Showcase -->
    <section class="space-y-4">
        <?php component('admin/components/tables/table-header', [
            'title' => 'Recent Products Inventory',
            'count' => 3
        ]); ?>

        <!-- Filters area -->
        <?php 
        ob_start();
        component('admin/components/forms/select-field', [
            'label' => 'Stock Status',
            'name' => 'stock_filter',
            'id' => 'stock_filter',
            'options' => [
                ['value' => 'all', 'label' => 'All Statuses'],
                ['value' => 'instock', 'label' => 'In Stock'],
                ['value' => 'outofstock', 'label' => 'Out of Stock'],
            ]
        ]);
        $filtersHtml = ob_get_clean();
        component('admin/components/tables/filters', ['content' => $filtersHtml]);
        ?>

        <!-- Table Grid -->
        <?php 
        $headers = [
            ['key' => 'id', 'label' => 'SKU ID', 'sortable' => true],
            ['key' => 'name', 'label' => 'Product Name', 'sortable' => true],
            ['key' => 'price', 'label' => 'Price', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'sortable' => false]
        ];
        $rows = [
            ['id' => 'GIFT-1002', 'name' => 'Premium Chocolate Gift Pack', 'price' => 'LKR 4,500', 'status' => 'active'],
            ['id' => 'GIFT-1005', 'name' => 'Fresh Red Roses Bouquet', 'price' => 'LKR 3,800', 'status' => 'pending'],
            ['id' => 'GIFT-1011', 'name' => 'Belgian Hazelnut Box', 'price' => 'LKR 6,200', 'status' => 'inactive']
        ];
        component('admin/components/tables/data-table', [
            'headers' => $headers,
            'rows' => $rows,
            'editUrlPrefix' => '?route=component-showcase&edit=',
            'deleteUrlPrefix' => '?route=component-showcase&delete='
        ]);
        ?>

        <!-- Bulk & Pagination row -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 border border-slate-200 border-t-0 rounded-b-card -mt-4 shadow-xs">
            <?php component('admin/components/tables/bulk-actions', [
                'actions' => [['value' => 'delete', 'label' => 'Delete Selected Items']]
            ]); ?>
            <?php component('admin/components/common/pagination', [
                'currentPage' => 1,
                'totalPages' => 5,
                'baseUrl' => '?route=component-showcase'
            ]); ?>
        </div>
    </section>

    <!-- 3. Form Validation & Feedbacks -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Validation and Error feedbacks -->
        <div class="bg-white border border-slate-200 rounded-card shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b pb-2 mb-4">Feedback & Validation</h3>
            <?php component('admin/components/feedback/validation-summary', [
                'errors' => [
                    'The Product Name field is required.',
                    'The Price must be a positive integer value.',
                    'The uploaded file exceeds the maximum 2MB size limit.'
                ]
            ]); ?>
            <?php component('admin/components/feedback/alert', [
                'message' => 'Database records updated successfully.',
                'type' => 'success'
            ]); ?>
        </div>

        <!-- Form Elements -->
        <div class="bg-white border border-slate-200 rounded-card shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b pb-2 mb-4">CRUD Fields Layout</h3>
            <form action="#" method="POST" class="space-y-4" onsubmit="return false;">
                <?php component('admin/components/forms/form-field', [
                    'label' => 'Product title',
                    'name' => 'prod_title',
                    'id' => 'prod_title',
                    'value' => 'Premium Chocolate Gift Pack',
                    'required' => true
                ]); ?>

                <?php component('admin/components/forms/slug-field', [
                    'label' => 'Clean slug segment (Automated)',
                    'name' => 'prod_slug',
                    'id' => 'prod_slug',
                    'value' => 'premium-chocolate-gift-pack',
                    'sourceId' => 'prod_title'
                ]); ?>

                <?php component('admin/components/forms/image-upload', [
                    'label' => 'Upload Featured Product Image',
                    'name' => 'prod_image',
                    'id' => 'prod_image',
                    'currentImageURL' => 'https://placehold.co/100x100/png'
                ]); ?>

                <!-- SEO details group -->
                <?php component('admin/components/forms/seo-field-group', [
                    'metaTitle' => 'Premium Chocolate Gift Pack | Gift Vibe LK',
                    'metaDescription' => 'Order premium chocolate gift packs online in Sri Lanka. Fast delivery.'
                ]); ?>

                <!-- Actions -->
                <?php component('admin/components/forms/form-actions', [
                    'backUrl' => '#',
                    'submitLabel' => 'Save Product Record'
                ]); ?>
            </form>
        </div>
    </section>

    <!-- Modal Elements definition -->
    <?php component('admin/components/common/modal', [
        'id' => 'admin-showcase-modal',
        'title' => 'Administrative System Prompt',
        'content' => '<p>You are viewing the administrative design system showcase. Focus outlines are highlighted globally to keep the interface highly accessible for screen reader and keyboard actions.</p>',
        'footer' => '
            <button type="button" class="px-4 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 text-sm font-semibold rounded-button" onclick="closeModal(\'#admin-showcase-modal\')">Close</button>
            <button type="button" class="px-4 py-2 bg-primary hover:bg-primary-600 text-white text-sm font-semibold rounded-button" onclick="closeModal(\'#admin-showcase-modal\')">Confirm Action</button>
        '
    ]); ?>
</div>
