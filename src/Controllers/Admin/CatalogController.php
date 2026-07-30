<?php

namespace Controllers\Admin;

use App\Core\Database;
use App\Core\View;
use Helpers\AdminNavigation;

class CatalogController
{
    private array $statuses = ['draft', 'active', 'inactive', 'archived'];

    public function products(?string $error = null): void
    {
        $products = Database::instance()->fetchAll(
            'SELECT products.*,
                    COALESCE((SELECT image_path FROM product_images WHERE product_images.product_id = products.id ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1), "public/assets/images/hero_gift_box.jpg") AS image_path,
                    COALESCE((SELECT AVG(rating) FROM reviews WHERE reviews.product_id = products.id AND reviews.status = "approved"), 0) AS average_rating,
                    (SELECT COUNT(*) FROM reviews WHERE reviews.product_id = products.id AND reviews.status = "approved") AS reviews_count,
                    COALESCE((SELECT GROUP_CONCAT(category_id) FROM product_categories WHERE product_categories.product_id = products.id), "") AS category_ids
             FROM products
             ORDER BY products.created_at DESC
             LIMIT 150'
        );

        $categories = Database::instance()->fetchAll(
            'SELECT id, name FROM categories WHERE status = "active" ORDER BY name ASC'
        );

        View::renderPage('admin/pages/catalog/products', [
            'title' => 'Products',
            'adminNavigation' => AdminNavigation::make('products'),
            'breadcrumbs' => [['label' => 'Admin', 'url' => url('/admin/dashboard')], ['label' => 'Products']],
            'products' => $products,
            'categories' => $categories,
            'statuses' => $this->statuses,
            'error' => $error,
        ], 'admin/layouts/admin');
    }

    public function storeProduct(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->products('Your session expired. Try again.');
            return;
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            $this->products('Product name is required.');
            return;
        }

        $sku = $this->uniqueProductSku($name);
        $slug = $this->uniqueProductSlug(trim((string) ($_POST['slug'] ?? '')) ?: $name);
        $image = $this->uploadProductImage($name);
        if ($image === null) {
            $this->products('Product image is required. Upload a JPG, PNG, or WebP image under 5MB.');
            return;
        }
        $offerType = in_array($_POST['offer_type'] ?? '', ['none', 'fixed', 'percentage'], true) ? $_POST['offer_type'] : 'none';

        Database::instance()->execute(
            'INSERT INTO products (sku, name, slug, short_description, description, base_price, sale_price, cost_price, offer_type, offer_value, unit, stock_quantity, low_stock_threshold, is_featured, status)
             VALUES (:sku, :name, :slug, :short_description, :description, :base_price, :sale_price, :cost_price, :offer_type, :offer_value, :unit, :stock_quantity, :low_stock_threshold, :is_featured, :status)',
            [
                'sku' => $sku,
                'name' => $name,
                'slug' => $slug,
                'short_description' => trim((string) ($_POST['short_description'] ?? '')),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'base_price' => (float) ($_POST['base_price'] ?? 0),
                'sale_price' => ($_POST['sale_price'] ?? '') === '' ? null : (float) $_POST['sale_price'],
                'cost_price' => (float) ($_POST['cost_price'] ?? 0),
                'offer_type' => $offerType,
                'offer_value' => $offerType !== 'none' ? (float) ($_POST['offer_value'] ?? 0) : null,
                'unit' => trim((string) ($_POST['unit'] ?? '')),
                'stock_quantity' => (int) ($_POST['stock_quantity'] ?? 0),
                'low_stock_threshold' => (int) ($_POST['low_stock_threshold'] ?? 5),
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'status' => in_array($_POST['status'] ?? '', $this->statuses, true) ? $_POST['status'] : 'active',
            ]
        );
        $productId = (int) Database::instance()->connection()->lastInsertId();

        // Assign categories
        $categoryIds = isset($_POST['category_ids']) ? (array) $_POST['category_ids'] : [];
        $categoryIds = array_map('intval', $categoryIds);
        $categoryIds = array_unique(array_filter($categoryIds));
        foreach ($categoryIds as $categoryId) {
            Database::instance()->execute(
                'INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)',
                ['product_id' => $productId, 'category_id' => $categoryId]
            );
        }

        // Insert primary image
        Database::instance()->execute(
            'INSERT INTO product_images (product_id, image_path, alt_text, is_primary) VALUES (:product_id, :image_path, :alt_text, 1)',
            ['product_id' => $productId, 'image_path' => $image, 'alt_text' => $name . ' gift image']
        );

        // Handle variants (multi color / design)
        $variantNames = isset($_POST['variant_name']) ? (array) $_POST['variant_name'] : [];
        $variantColors = isset($_POST['variant_color']) ? (array) $_POST['variant_color'] : [];
        $variantImages = isset($_POST['variant_image']) ? (array) $_POST['variant_image'] : [];
        $variantPrice = isset($_POST['variant_price']) ? (array) $_POST['variant_price'] : [];
        $variantStock = isset($_POST['variant_stock']) ? (array) $_POST['variant_stock'] : [];

        foreach ($variantNames as $i => $vName) {
            $vName = trim((string) $vName);
            if ($vName === '') continue;
            $vSku = $sku . '-' . $this->slug($vName) . '-' . ($i + 1);
            $vColor = trim((string) ($variantColors[$i] ?? ''));
            $vImg = trim((string) ($variantImages[$i] ?? ''));
            $vPrice = (float) ($variantPrice[$i] ?? 0);
            $vStock = (int) ($variantStock[$i] ?? 0);

            // Handle variant image upload
            $variantImagePath = null;
            $fileKey = 'variant_image_file_' . $i;
            if (!empty($_FILES[$fileKey]['tmp_name']) && is_uploaded_file($_FILES[$fileKey]['tmp_name'])) {
                $variantImagePath = $this->uploadVariantImage($_FILES[$fileKey], $productId, $i);
            } elseif ($vImg !== '') {
                $variantImagePath = $vImg;
            }

            Database::instance()->execute(
                'INSERT INTO product_variants (product_id, name, sku, color_name, color_hex, image_path, price_adjustment, stock_quantity, status)
                 VALUES (:product_id, :name, :sku, :color_name, :color_hex, :image_path, :price_adjustment, :stock_quantity, :status)',
                [
                    'product_id' => $productId,
                    'name' => $vName,
                    'sku' => $vSku,
                    'color_name' => $vColor,
                    'color_hex' => null,
                    'image_path' => $variantImagePath,
                    'price_adjustment' => $vPrice,
                    'stock_quantity' => $vStock,
                    'status' => 'active',
                ]
            );
        }

        redirect('/admin/products');
    }

    public function updateProduct(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->products('Your session expired. Try again.');
            return;
        }

        $productId = (int) $id;
        $product = Database::instance()->fetch('SELECT * FROM products WHERE id = :id LIMIT 1', ['id' => $productId]);
        if (!$product) {
            $this->products('Product not found.');
            return;
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            $this->products('Product name is required.');
            return;
        }

        Database::instance()->execute(
            'UPDATE products
             SET name = :name,
                 slug = :slug,
                 short_description = :short_description,
                 description = :description,
                 base_price = :base_price,
                 sale_price = :sale_price,
                 cost_price = :cost_price,
                 offer_type = :offer_type,
                 offer_value = :offer_value,
                 unit = :unit,
                 stock_quantity = :stock_quantity,
                 low_stock_threshold = :low_stock_threshold,
                 is_featured = :is_featured,
                 status = :status
             WHERE id = :id',
            [
                'id' => $productId,
                'name' => $name,
                'slug' => $this->uniqueProductSlug(trim((string) ($_POST['slug'] ?? '')) ?: $name, $productId),
                'short_description' => trim((string) ($_POST['short_description'] ?? '')),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'base_price' => (float) ($_POST['base_price'] ?? 0),
                'sale_price' => ($_POST['sale_price'] ?? '') === '' ? null : (float) $_POST['sale_price'],
                'cost_price' => (float) ($product['cost_price'] ?? 0),
                'offer_type' => in_array($_POST['offer_type'] ?? '', ['none', 'fixed', 'percentage'], true) ? $_POST['offer_type'] : 'none',
                'offer_value' => ($_POST['offer_type'] ?? '') !== 'none' ? (float) ($_POST['offer_value'] ?? 0) : null,
                'unit' => trim((string) ($_POST['unit'] ?? '')),
                'stock_quantity' => (int) ($_POST['stock_quantity'] ?? 0),
                'low_stock_threshold' => (int) ($_POST['low_stock_threshold'] ?? 5),
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'status' => in_array($_POST['status'] ?? '', $this->statuses, true) ? $_POST['status'] : 'active',
            ]
        );

        Database::instance()->execute('DELETE FROM product_categories WHERE product_id = :product_id', ['product_id' => $productId]);
        $categoryIds = isset($_POST['category_ids']) ? (array) $_POST['category_ids'] : [];
        $categoryIds = array_unique(array_filter(array_map('intval', $categoryIds)));
        foreach ($categoryIds as $categoryId) {
            Database::instance()->execute(
                'INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)',
                ['product_id' => $productId, 'category_id' => $categoryId]
            );
        }

        $image = $this->uploadProductImage($name, false);
        if ($image !== null) {
            Database::instance()->execute('UPDATE product_images SET is_primary = 0 WHERE product_id = :product_id', ['product_id' => $productId]);
            Database::instance()->execute(
                'INSERT INTO product_images (product_id, image_path, alt_text, is_primary) VALUES (:product_id, :image_path, :alt_text, 1)',
                ['product_id' => $productId, 'image_path' => $image, 'alt_text' => $name . ' gift image']
            );
        }

        redirect('/admin/products');
    }

    public function productShow(string $id): void
    {
        $productId = (int) $id;
        $product = Database::instance()->fetch(
            'SELECT products.*,
                    COALESCE((SELECT image_path FROM product_images WHERE product_images.product_id = products.id ORDER BY is_primary DESC, sort_order ASC, id ASC LIMIT 1), "public/assets/images/hero_gift_box.jpg") AS image_path
             FROM products
             WHERE products.id = :id
             LIMIT 1',
            ['id' => $productId]
        );

        if (!$product) {
            redirect('/admin/products');
            return;
        }

        $stats = Database::instance()->fetch(
            'SELECT
                COALESCE((SELECT SUM(order_items.quantity)
                    FROM order_items
                    INNER JOIN orders ON orders.id = order_items.order_id
                    WHERE order_items.product_id = products.id AND orders.order_status <> "cancelled"), 0) AS sold_count,
                COALESCE((SELECT SUM(order_items.total_price)
                    FROM order_items
                    INNER JOIN orders ON orders.id = order_items.order_id
                    WHERE order_items.product_id = products.id AND orders.order_status <> "cancelled"), 0) AS sales_total,
                COALESCE((SELECT SUM(order_items.cost_price * order_items.quantity)
                    FROM order_items
                    INNER JOIN orders ON orders.id = order_items.order_id
                    WHERE order_items.product_id = products.id AND orders.order_status <> "cancelled"), 0) AS cost_total,
                COALESCE((SELECT AVG(rating) FROM reviews WHERE reviews.product_id = products.id AND reviews.status = "approved"), 0) AS average_rating,
                (SELECT COUNT(*) FROM reviews WHERE reviews.product_id = products.id AND reviews.status = "approved") AS reviews_count,
                (SELECT COUNT(*) FROM wishlists WHERE wishlists.product_id = products.id) AS wishlist_count,
                (SELECT COUNT(DISTINCT orders.id)
                    FROM order_items
                    INNER JOIN orders ON orders.id = order_items.order_id
                    WHERE order_items.product_id = products.id AND orders.order_status <> "cancelled") AS order_count
             FROM products
             WHERE products.id = :id',
            ['id' => $productId]
        ) ?? [];

        $categories = Database::instance()->fetchAll(
            'SELECT categories.name
             FROM categories
             INNER JOIN product_categories ON product_categories.category_id = categories.id
             WHERE product_categories.product_id = :product_id
             ORDER BY categories.name ASC',
            ['product_id' => $productId]
        );

        $images = Database::instance()->fetchAll(
            'SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_primary DESC, sort_order ASC, id ASC',
            ['product_id' => $productId]
        );

        $variants = Database::instance()->fetchAll(
            'SELECT product_variants.*,
                    COALESCE((SELECT SUM(order_items.quantity)
                        FROM order_items
                        INNER JOIN orders ON orders.id = order_items.order_id
                        WHERE order_items.variant_id = product_variants.id
                          AND orders.order_status <> "cancelled"), 0) AS sold_count,
                    (SELECT COUNT(DISTINCT orders.id)
                        FROM order_items
                        INNER JOIN orders ON orders.id = order_items.order_id
                        WHERE order_items.variant_id = product_variants.id
                          AND orders.order_status <> "cancelled") AS order_count,
                    COALESCE((SELECT AVG(reviews.rating)
                        FROM reviews
                        INNER JOIN orders ON orders.id = reviews.order_id
                        INNER JOIN order_items ON order_items.order_id = orders.id
                        WHERE order_items.variant_id = product_variants.id
                          AND reviews.product_id = product_variants.product_id
                          AND reviews.status = "approved"), 0) AS average_rating,
                    (SELECT COUNT(DISTINCT reviews.id)
                        FROM reviews
                        INNER JOIN orders ON orders.id = reviews.order_id
                        INNER JOIN order_items ON order_items.order_id = orders.id
                        WHERE order_items.variant_id = product_variants.id
                          AND reviews.product_id = product_variants.product_id
                          AND reviews.status = "approved") AS reviews_count,
                    (SELECT COUNT(*)
                        FROM wishlists
                        WHERE wishlists.product_id = product_variants.product_id) AS likes_count
             FROM product_variants
             WHERE product_variants.product_id = :product_id
             ORDER BY product_variants.id ASC',
            ['product_id' => $productId]
        );

        $reviews = Database::instance()->fetchAll(
            'SELECT reviews.*, users.first_name, users.last_name
             FROM reviews
             LEFT JOIN users ON users.id = reviews.user_id
             WHERE reviews.product_id = :product_id
             ORDER BY reviews.created_at DESC
             LIMIT 25',
            ['product_id' => $productId]
        );

        $orders = Database::instance()->fetchAll(
            'SELECT orders.id, orders.order_number, orders.customer_name, orders.order_status, orders.created_at,
                    order_items.variant_id, order_items.quantity, order_items.unit_price, order_items.total_price
             FROM order_items
             INNER JOIN orders ON orders.id = order_items.order_id
             WHERE order_items.product_id = :product_id
             ORDER BY orders.created_at DESC
             LIMIT 20',
            ['product_id' => $productId]
        );

        View::renderPage('admin/pages/catalog/product-show', [
            'title' => $product['name'],
            'adminNavigation' => AdminNavigation::make('products'),
            'breadcrumbs' => [
                ['label' => 'Admin', 'url' => url('/admin/dashboard')],
                ['label' => 'Products', 'url' => url('/admin/products')],
                ['label' => $product['name']],
            ],
            'product' => $product,
            'stats' => $stats,
            'categories' => $categories,
            'images' => $images,
            'variants' => $variants,
            'reviews' => $reviews,
            'orders' => $orders,
        ], 'admin/layouts/admin');
    }

    private function uploadProductImage(string $name, bool $required = true): ?string
    {
        if (empty($_FILES['product_image']['tmp_name']) || !is_uploaded_file($_FILES['product_image']['tmp_name'])) {
            return null;
        }

        $tmp = $_FILES['product_image']['tmp_name'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($tmp) ?: '';
        if (!isset($allowed[$mime]) || (int) ($_FILES['product_image']['size'] ?? 0) > 5 * 1024 * 1024) {
            return null;
        }

        $directory = PUBLIC_PATH . '/assets/images/products/uploads';
        if (!is_dir($directory)) mkdir($directory, 0775, true);

        $relative = 'public/assets/images/products/uploads/' . $this->slug($name) . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];

        return move_uploaded_file($tmp, BASE_PATH . '/' . $relative) ? $relative : null;
    }

    private function uploadVariantImage(array $file, int $productId, int $index): ?string
    {
        $tmp = $file['tmp_name'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($tmp) ?: '';
        if (!isset($allowed[$mime]) || (int) ($file['size'] ?? 0) > 5 * 1024 * 1024) return null;

        $directory = PUBLIC_PATH . '/assets/images/products/variants';
        if (!is_dir($directory)) mkdir($directory, 0775, true);

        $relative = 'public/assets/images/products/variants/product-' . $productId . '-var-' . $index . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
        return move_uploaded_file($tmp, BASE_PATH . '/' . $relative) ? $relative : null;
    }

    public function productStatus(string $id): void
    {
        if (verify_csrf($_POST['_token'] ?? null)) {
            $status = in_array($_POST['status'] ?? '', $this->statuses, true) ? $_POST['status'] : 'active';
            Database::instance()->execute('UPDATE products SET status = :status WHERE id = :id', ['status' => $status, 'id' => (int) $id]);
        }
        redirect('/admin/products');
    }

    public function categories(?string $error = null): void
    {
        $categories = Database::instance()->fetchAll(
            'SELECT categories.*,
                    (SELECT COUNT(*) FROM product_categories WHERE product_categories.category_id = categories.id) AS products_count
             FROM categories
             ORDER BY categories.created_at DESC, categories.id DESC'
        );
        foreach ($categories as &$category) {
            $imagePath = (string) ($category['image_path'] ?? '');
            $generatedSvgPaths = [
                'public/assets/images/categories/birthday-gifts.svg',
                'public/assets/images/categories/luxury-hampers.svg',
                'public/assets/images/categories/flowers-chocolates.svg',
            ];
            if ($imagePath === '' || $imagePath === 'public/assets/images/hero_gift_box.jpg' || in_array($imagePath, $generatedSvgPaths, true)) {
                $category['image_path'] = $this->categoryArtworkFor((string) ($category['name'] ?? ''));
            }
        }
        unset($category);

        View::renderPage('admin/pages/catalog/categories', [
            'title' => 'Categories',
            'subtitle' => 'Manage your product categories',
            'adminNavigation' => AdminNavigation::make('categories'),
            'breadcrumbs' => [['label' => 'Catalog'], ['label' => 'Categories']],
            'categories' => $categories,
            'error' => $error,
        ], 'admin/layouts/admin');
    }

    public function storeCategory(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->categories('Your session expired. Try again.');
            return;
        }
        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            $this->categories('Category name is required.');
            return;
        }

        $image = $this->categoryImageUpload();
        if ($image === null) {
            $image = trim((string) ($_POST['image_path'] ?? '')) ?: $this->categoryArtworkFor($name);
        }

        Database::instance()->execute(
            'INSERT INTO categories (name, slug, description, image_path, sort_order, status)
             VALUES (:name, :slug, :description, :image_path, :sort_order, :status)',
            [
                'name' => $name,
                'slug' => $this->uniqueCategorySlug(trim((string) ($_POST['slug'] ?? '')) ?: $name),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'image_path' => $image,
                'sort_order' => (int) ($_POST['sort_order'] ?? 0),
                'status' => ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active',
            ]
        );

        redirect('/admin/categories?saved=created');
    }

    public function updateCategory(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->categories('Your session expired. Try again.');
            return;
        }

        $categoryId = (int) $id;
        $category = Database::instance()->fetch('SELECT * FROM categories WHERE id = :id LIMIT 1', ['id' => $categoryId]);
        if (!$category) {
            $this->categories('Category not found.');
            return;
        }

        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name === '') {
            $this->categories('Category name is required.');
            return;
        }

        $uploadedImage = $this->categoryImageUpload();
        $image = $uploadedImage
            ?? (trim((string) ($_POST['image_path'] ?? '')) ?: (string) ($category['image_path'] ?? $this->categoryArtworkFor($name)));

        Database::instance()->execute(
            'UPDATE categories
             SET name = :name,
                 slug = :slug,
                 description = :description,
                 image_path = :image_path,
                 sort_order = :sort_order,
                 status = :status
             WHERE id = :id',
            [
                'id' => $categoryId,
                'name' => $name,
                'slug' => $this->uniqueCategorySlug(trim((string) ($_POST['slug'] ?? '')) ?: $name, $categoryId),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'image_path' => $image,
                'sort_order' => (int) ($_POST['sort_order'] ?? 0),
                'status' => ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active',
            ]
        );

        redirect('/admin/categories?saved=updated');
    }

    public function categoryStatus(string $id): void
    {
        if (verify_csrf($_POST['_token'] ?? null)) {
            $status = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';
            Database::instance()->execute('UPDATE categories SET status = :status WHERE id = :id', ['status' => $status, 'id' => (int) $id]);
        }
        redirect('/admin/categories');
    }

    public function deleteCategory(string $id): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->categories('Your session expired. Try again.');
            return;
        }

        $categoryId = (int) $id;
        $category = Database::instance()->fetch('SELECT id FROM categories WHERE id = :id LIMIT 1', ['id' => $categoryId]);
        if (!$category) {
            $this->categories('Category not found.');
            return;
        }

        Database::instance()->execute('DELETE FROM categories WHERE id = :id', ['id' => $categoryId]);
        redirect('/admin/categories?saved=deleted');
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $value), '-'));
        return $slug !== '' ? $slug : 'item-' . bin2hex(random_bytes(3));
    }

    private function uniqueProductSku(string $name): string
    {
        $base = strtoupper(preg_replace('/[^A-Z0-9]+/', '-', $this->slug($name)));
        $base = trim($base, '-');
        if ($base === '') {
            $base = 'PRODUCT';
        }
        $base = 'GV-' . substr($base, 0, 24);
        $sku = $base;
        $suffix = 1;

        while (Database::instance()->fetch('SELECT id FROM products WHERE sku = :sku LIMIT 1', ['sku' => $sku])) {
            $sku = $base . '-' . str_pad((string) $suffix++, 3, '0', STR_PAD_LEFT);
        }

        return $sku;
    }

    private function uniqueProductSlug(string $value, ?int $ignoreId = null): string
    {
        $base = $this->slug($value);
        $slug = $base;
        $suffix = 2;

        while (true) {
            $params = ['slug' => $slug];
            $sql = 'SELECT id FROM products WHERE slug = :slug';
            if ($ignoreId !== null) {
                $sql .= ' AND id <> :id';
                $params['id'] = $ignoreId;
            }
            $sql .= ' LIMIT 1';

            if (!Database::instance()->fetch($sql, $params)) {
                return $slug;
            }

            $slug = $base . '-' . $suffix++;
        }
    }

    private function uniqueCategorySlug(string $value, ?int $ignoreId = null): string
    {
        $base = $this->slug($value);
        $slug = $base;
        $suffix = 2;

        while (true) {
            $params = ['slug' => $slug];
            $sql = 'SELECT id FROM categories WHERE slug = :slug';
            if ($ignoreId !== null) {
                $sql .= ' AND id <> :id';
                $params['id'] = $ignoreId;
            }
            $sql .= ' LIMIT 1';

            if (!Database::instance()->fetch($sql, $params)) {
                return $slug;
            }

            $slug = $base . '-' . $suffix++;
        }
    }

    private function categoryImageUpload(): ?string
    {
        if (empty($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
            return null;
        }

        $tmp = $_FILES['image']['tmp_name'];
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        ];
        $mime = mime_content_type($tmp) ?: '';

        if (!isset($allowed[$mime]) || (int) ($_FILES['image']['size'] ?? 0) > 5 * 1024 * 1024) {
            return null;
        }

        $directory = PUBLIC_PATH . '/assets/images/categories/uploads';
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $relative = 'public/assets/images/categories/uploads/category-' . date('YmdHis') . '-' . bin2hex(random_bytes(5)) . '.' . $allowed[$mime];

        return move_uploaded_file($tmp, BASE_PATH . '/' . $relative) ? $relative : null;
    }

    private function categoryArtworkFor(string $name): string
    {
        $normalized = strtolower($name);
        $compact = preg_replace('/[^a-z0-9]+/', '-', $normalized);

        $exact = [
            'birthday-gifts' => 'birthday-gifts.png',
            'luxury-hampers' => 'luxury-hampers.png',
            'flowers-chocolates' => 'flowers-chocolates.png',
            'flower-chocolates' => 'flowers-chocolates.png',
            'chocolate-gifts' => 'chocolate-gifts.svg',
            'flower-gifts' => 'flower-gifts.svg',
            'personalized-gifts' => 'personalized-gifts.svg',
            'personalised-gifts' => 'personalized-gifts.svg',
            'baby-gifts' => 'baby-gifts.svg',
            'wedding-gifts' => 'wedding-gifts.svg',
            'corporate-gifts' => 'corporate-gifts.svg',
            'valentine-gifts' => 'valentine-gifts.svg',
            'graduation-gifts' => 'graduation-gifts.svg',
        ];

        if (isset($exact[$compact])) {
            return 'public/assets/images/categories/' . $exact[$compact];
        }

        $map = [
            'birthday' => 'birthday-gifts.png',
            'luxury' => 'luxury-hampers.png',
            'hamper' => 'luxury-hampers.png',
            'bouquet' => 'flower-gifts.svg',
            'flower' => 'flower-gifts.svg',
            'chocolate' => 'chocolate-gifts.svg',
            'personal' => 'personalized-gifts.svg',
            'custom' => 'personalized-gifts.svg',
            'baby' => 'baby-gifts.svg',
            'wedding' => 'wedding-gifts.svg',
            'corporate' => 'corporate-gifts.svg',
            'valentine' => 'valentine-gifts.svg',
            'graduation' => 'graduation-gifts.svg',
            'gift' => 'category-fallback.svg',
            'box' => 'category-fallback.svg',
        ];

        foreach ($map as $needle => $file) {
            if (str_contains($normalized, $needle)) {
                return 'public/assets/images/categories/' . $file;
            }
        }

        return 'public/assets/images/categories/category-fallback.svg';
    }
}
