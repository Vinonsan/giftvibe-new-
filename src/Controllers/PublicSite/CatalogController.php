<?php

namespace Controllers\PublicSite;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;

class CatalogController
{
    private int $perPage = 12;

    public function home(): void
    {
        View::renderPage('public/pages/home', [
            'title' => 'GiftVibe.lk | Premium Gift Delivery in Sri Lanka',
            'navigation' => $this->navigation('home'),
            'featuredProducts' => $this->products(['featured' => true], 1, 8)['items'],
            'offerProducts' => $this->products(['offers' => true], 1, 12)['items'],
            'latestProducts' => $this->products([], 1, 8)['items'],
            'categories' => $this->categories(8),
            'deliveredTrust' => $this->deliveredTrust(),
            'meta' => [
                'description' => 'Shop published gift boxes, flowers, chocolates, and premium surprises for delivery across Sri Lanka.',
                'image' => asset('/public/assets/images/hero_gift_box.jpg'),
                'url' => url('/'),
            ],
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'Store',
                'name' => 'GiftVibe.lk',
                'url' => url('/'),
                'image' => asset('/public/assets/images/hero_gift_box.jpg'),
                'priceRange' => 'LKR',
            ],
        ]);
    }

    public function shop(): void
    {
        $filters = $this->filters();
        $page = $this->page();
        $result = $this->products($filters, $page, $this->perPage);

        View::renderPage('public/pages/shop', [
            'title' => 'Shop Published Gifts | GiftVibe.lk',
            'navigation' => $this->navigation('shop'),
            'products' => $result['items'],
            'categories' => $this->categories(),
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $result['totalPages'],
            'baseUrl' => $this->paginationUrl('/shop', $filters),
            'heading' => 'Shop Published Gifts',
            'description' => 'Browse gifts that are currently published and available on GiftVibe.lk.',
            'meta' => [
                'description' => 'Browse published GiftVibe.lk gifts, offers, featured picks, and search results.',
                'url' => url('/shop'),
            ],
            'structuredData' => $this->itemListStructuredData($result['items']),
        ]);
    }

    public function search(): void
    {
        $this->shop();
    }

    public function categoriesPage(): void
    {
        View::renderPage('public/pages/categories', [
            'title' => 'Gift Categories | GiftVibe.lk',
            'navigation' => $this->navigation('categories'),
            'categories' => $this->categories(),
            'meta' => [
                'description' => 'Explore GiftVibe.lk gift categories and discover published products for every occasion.',
                'url' => url('/categories'),
            ],
        ]);
    }

    public function about(): void
    {
        $stats = Database::instance()->fetch(
            'SELECT
                (SELECT COUNT(*) FROM products WHERE status = "active") AS products,
                (SELECT COUNT(*) FROM categories WHERE status = "active") AS categories,
                (SELECT COUNT(*) FROM orders WHERE order_status = "delivered") AS delivered_orders,
                (SELECT COUNT(*) FROM reviews WHERE status = "approved") AS approved_reviews'
        ) ?? [];

        View::renderPage('public/pages/about', [
            'title' => 'About GiftVibe.lk | Modern Gift Delivery in Sri Lanka',
            'navigation' => $this->navigation('about'),
            'stats' => $stats,
            'meta' => [
                'description' => 'Learn about GiftVibe.lk, a modern Sri Lankan gifting platform for curated gifts, custom requests, and thoughtful delivery experiences.',
                'image' => asset('/public/assets/images/hero_gift_box.jpg'),
                'url' => url('/about'),
            ],
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'About GiftVibe.lk',
                'url' => url('/about'),
            ],
        ]);
    }

    public function category(string $slug): void
    {
        $category = Database::instance()->fetch(
            'SELECT * FROM categories WHERE slug = :slug AND status = "active" LIMIT 1',
            ['slug' => $slug]
        );

        if (!$category) {
            http_response_code(404);
            View::renderPage('public/pages/404', ['title' => 'Category Not Found | GiftVibe.lk']);
            return;
        }

        $filters = $this->filters();
        $filters['category'] = $slug;
        $page = $this->page();
        $result = $this->products($filters, $page, $this->perPage);

        View::renderPage('public/pages/shop', [
            'title' => $category['name'] . ' Gifts | GiftVibe.lk',
            'navigation' => $this->navigation('categories'),
            'products' => $result['items'],
            'categories' => $this->categories(),
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $result['totalPages'],
            'baseUrl' => $this->paginationUrl('/category/' . $slug, $filters, ['category']),
            'heading' => $category['name'],
            'description' => $category['description'] ?: 'Published gifts in this GiftVibe.lk category.',
            'meta' => [
                'description' => $category['meta_description'] ?: ($category['description'] ?: 'Shop published ' . $category['name'] . ' gifts on GiftVibe.lk.'),
                'image' => $this->imageUrl($category['image_path'] ?? null),
                'url' => url('/category/' . $slug),
            ],
            'structuredData' => $this->itemListStructuredData($result['items']),
        ]);
    }

    public function product(string $slug): void
    {
        $product = $this->publishedProductBySlug($slug);
        if (!$product) {
            http_response_code(404);
            View::renderPage('public/pages/404', ['title' => 'Product Not Found | GiftVibe.lk']);
            return;
        }

        $images = Database::instance()->fetchAll(
            'SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_primary DESC, sort_order ASC, id ASC',
            ['product_id' => $product['id']]
        );
        $variants = Database::instance()->fetchAll(
            'SELECT * FROM product_variants WHERE product_id = :product_id AND status = "active" ORDER BY id ASC',
            ['product_id' => $product['id']]
        );
        $categories = Database::instance()->fetchAll(
            'SELECT categories.* FROM categories
             INNER JOIN product_categories ON product_categories.category_id = categories.id
             WHERE product_categories.product_id = :product_id AND categories.status = "active"',
            ['product_id' => $product['id']]
        );
        $reviews = Database::instance()->fetchAll(
            'SELECT reviews.*, users.first_name, users.last_name
             FROM reviews
             LEFT JOIN users ON users.id = reviews.user_id
             WHERE reviews.product_id = :product_id AND reviews.status = "approved"
             ORDER BY reviews.created_at DESC',
            ['product_id' => $product['id']]
        );
        $rating = Database::instance()->fetch(
            'SELECT COALESCE(AVG(rating), 0) AS average_rating, COUNT(*) AS reviews_count
             FROM reviews
             WHERE product_id = :product_id AND status = "approved"',
            ['product_id' => $product['id']]
        ) ?? ['average_rating' => 0, 'reviews_count' => 0];

        View::renderPage('public/pages/product-detail', [
            'title' => $product['name'] . ' | GiftVibe.lk',
            'navigation' => $this->navigation('shop'),
            'product' => $product,
            'images' => $images,
            'variants' => $variants,
            'categories' => $categories,
            'reviews' => $reviews,
            'averageRating' => (float) $rating['average_rating'],
            'reviewsCount' => (int) $rating['reviews_count'],
            'relatedProducts' => $this->relatedProducts((int) $product['id'], array_column($categories, 'id')),
            'meta' => [
                'description' => $product['meta_description'] ?: ($product['short_description'] ?: 'Shop ' . $product['name'] . ' on GiftVibe.lk.'),
                'image' => $this->imageUrl($product['primary_image'] ?? null),
                'url' => url('/product/' . $slug),
            ],
            'structuredData' => $this->productStructuredData($product, $images, (float) $rating['average_rating'], (int) $rating['reviews_count']),
        ]);
    }

    public function toggleWishlist(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null) || !Auth::isCustomer()) {
            redirect('/login');
        }

        $productId = (int) ($_POST['product_id'] ?? 0);
        $back = (string) ($_POST['redirect_to'] ?? '/shop');
        $product = Database::instance()->fetch('SELECT id FROM products WHERE id = :id AND status = "active"', ['id' => $productId]);

        if ($product) {
            $existing = Database::instance()->fetch(
                'SELECT id FROM wishlists WHERE user_id = :user_id AND product_id = :product_id',
                ['user_id' => Auth::id(), 'product_id' => $productId]
            );

            Database::instance()->execute(
                $existing
                    ? 'DELETE FROM wishlists WHERE user_id = :user_id AND product_id = :product_id'
                    : 'INSERT INTO wishlists (user_id, product_id) VALUES (:user_id, :product_id)',
                ['user_id' => Auth::id(), 'product_id' => $productId]
            );
        }

        redirect($back);
    }

    private function products(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where = ['products.status = "active"'];
        $joins = [
            'LEFT JOIN product_images primary_image ON primary_image.product_id = products.id AND primary_image.is_primary = 1',
            'LEFT JOIN wishlists ON wishlists.product_id = products.id',
            'LEFT JOIN reviews approved_reviews ON approved_reviews.product_id = products.id AND approved_reviews.status = "approved"',
        ];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(products.name LIKE :q OR products.sku LIKE :q OR products.short_description LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['category'])) {
            $joins[] = 'INNER JOIN product_categories filtered_pc ON filtered_pc.product_id = products.id';
            $joins[] = 'INNER JOIN categories filtered_categories ON filtered_categories.id = filtered_pc.category_id';
            $where[] = 'filtered_categories.slug = :category_slug AND filtered_categories.status = "active"';
            $params['category_slug'] = $filters['category'];
        }
        if (!empty($filters['offers'])) {
            $where[] = 'products.sale_price IS NOT NULL AND products.sale_price > 0 AND products.sale_price < products.base_price';
        }
        if (!empty($filters['featured'])) {
            $where[] = 'products.is_featured = 1';
        }
        if (($filters['stock'] ?? '') === 'in_stock') {
            $where[] = 'products.stock_quantity > 0';
        } elseif (($filters['stock'] ?? '') === 'out_of_stock') {
            $where[] = 'products.stock_quantity <= 0';
        }

        $joinSql = implode(' ', array_unique($joins));
        $whereSql = implode(' AND ', $where);
        $total = (int) ((Database::instance()->fetch("SELECT COUNT(DISTINCT products.id) AS total FROM products {$joinSql} WHERE {$whereSql}", $params)['total'] ?? 0));
        $totalPages = max(1, (int) ceil($total / $perPage));
        $offset = max(0, ($page - 1) * $perPage);

        $items = Database::instance()->fetchAll(
            "SELECT products.*, COALESCE(primary_image.image_path, '') AS primary_image, COUNT(DISTINCT wishlists.id) AS like_count,
                    COALESCE(AVG(approved_reviews.rating), 0) AS average_rating,
                    COUNT(DISTINCT approved_reviews.id) AS reviews_count
             FROM products {$joinSql}
             WHERE {$whereSql}
             GROUP BY products.id, primary_image.image_path
             ORDER BY products.is_featured DESC, products.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'totalPages' => $totalPages];
    }

    private function publishedProductBySlug(string $slug): ?array
    {
        return Database::instance()->fetch(
            'SELECT products.*, COALESCE(primary_image.image_path, "") AS primary_image, COUNT(DISTINCT wishlists.id) AS like_count
             FROM products
             LEFT JOIN product_images primary_image ON primary_image.product_id = products.id AND primary_image.is_primary = 1
             LEFT JOIN wishlists ON wishlists.product_id = products.id
             WHERE products.slug = :slug AND products.status = "active"
             GROUP BY products.id, primary_image.image_path
             LIMIT 1',
            ['slug' => $slug]
        );
    }

    private function relatedProducts(int $productId, array $categoryIds): array
    {
        if (!$categoryIds) {
            return $this->products([], 1, 4)['items'];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        return Database::instance()->fetchAll(
            "SELECT products.*, COALESCE(primary_image.image_path, '') AS primary_image, COUNT(DISTINCT wishlists.id) AS like_count,
                    COALESCE(AVG(approved_reviews.rating), 0) AS average_rating,
                    COUNT(DISTINCT approved_reviews.id) AS reviews_count
             FROM products
             INNER JOIN product_categories ON product_categories.product_id = products.id
             LEFT JOIN product_images primary_image ON primary_image.product_id = products.id AND primary_image.is_primary = 1
             LEFT JOIN wishlists ON wishlists.product_id = products.id
             LEFT JOIN reviews approved_reviews ON approved_reviews.product_id = products.id AND approved_reviews.status = 'approved'
             WHERE products.status = 'active' AND products.id <> ? AND product_categories.category_id IN ({$placeholders})
             GROUP BY products.id, primary_image.image_path
             ORDER BY products.is_featured DESC, products.created_at DESC
             LIMIT 4",
            array_merge([$productId], $categoryIds)
        );
    }

    private function categories(?int $limit = null): array
    {
        $limitSql = $limit ? ' LIMIT ' . (int) $limit : '';
        return Database::instance()->fetchAll(
            'SELECT categories.*, COUNT(DISTINCT products.id) AS products_count
             FROM categories
             LEFT JOIN product_categories ON product_categories.category_id = categories.id
             LEFT JOIN products ON products.id = product_categories.product_id AND products.status = "active"
             WHERE categories.status = "active"
             GROUP BY categories.id
             ORDER BY categories.sort_order ASC, categories.name ASC' . $limitSql
        );
    }

    private function deliveredTrust(): array
    {
        $counts = Database::instance()->fetch(
            'SELECT
                COUNT(DISTINCT orders.id) AS delivered_orders,
                COUNT(DISTINCT orders.delivery_city) AS delivered_cities
             FROM orders
             LEFT JOIN deliveries ON deliveries.order_id = orders.id
             WHERE orders.order_status = "delivered" OR deliveries.delivery_status = "delivered"'
        ) ?? ['delivered_orders' => 0, 'delivered_cities' => 0];

        $items = Database::instance()->fetchAll(
            'SELECT
                orders.order_number,
                orders.recipient_name,
                orders.delivery_city,
                deliveries.delivered_date,
                order_items.product_name,
                COALESCE(product_images.image_path, "public/assets/images/hero_gift_box.jpg") AS image_path
             FROM orders
             INNER JOIN order_items ON order_items.order_id = orders.id
             LEFT JOIN deliveries ON deliveries.order_id = orders.id
             LEFT JOIN product_images ON product_images.product_id = order_items.product_id AND product_images.is_primary = 1
             WHERE orders.order_status = "delivered" OR deliveries.delivery_status = "delivered"
             ORDER BY COALESCE(deliveries.delivered_at, orders.updated_at, orders.created_at) DESC
             LIMIT 6'
        );

        return [
            'deliveredOrders' => (int) ($counts['delivered_orders'] ?? 0),
            'deliveredCities' => (int) ($counts['delivered_cities'] ?? 0),
            'items' => $items,
        ];
    }

    private function filters(): array
    {
        return [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'category' => trim((string) ($_GET['category'] ?? '')),
            'offers' => isset($_GET['offers']) && $_GET['offers'] === '1',
            'featured' => isset($_GET['featured']) && $_GET['featured'] === '1',
            'stock' => trim((string) ($_GET['stock'] ?? '')),
        ];
    }

    private function page(): int
    {
        return max(1, (int) ($_GET['page'] ?? 1));
    }

    private function paginationUrl(string $path, array $filters, array $exclude = []): string
    {
        $query = [];
        foreach ($filters as $key => $value) {
            if (in_array($key, $exclude, true) || $value === '' || $value === false || $value === null) {
                continue;
            }
            $query[$key] = $value === true ? '1' : $value;
        }
        return url($path) . ($query ? '?' . http_build_query($query) : '');
    }

    private function imageUrl(?string $path): string
    {
        if (!$path) {
            return asset('/public/assets/images/hero_gift_box.jpg');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return asset('/' . ltrim($path, '/'));
    }

    private function itemListStructuredData(array $products): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => array_map(static fn (array $product, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => url('/product/' . $product['slug']),
                'name' => $product['name'],
            ], $products, array_keys($products)),
        ];
    }

    private function productStructuredData(array $product, array $images, float $averageRating = 0, int $reviewsCount = 0): array
    {
        $imageList = array_map(fn ($image) => $this->imageUrl($image['image_path'] ?? null), $images);
        if (!$imageList) {
            $imageList[] = $this->imageUrl($product['primary_image'] ?? null);
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'sku' => $product['sku'],
            'image' => $imageList,
            'description' => $product['short_description'] ?: $product['description'],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'LKR',
                'price' => (float) ($product['sale_price'] ?: $product['base_price']),
                'availability' => ((int) $product['stock_quantity'] > 0) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => url('/product/' . $product['slug']),
            ],
        ];
        if ($reviewsCount > 0) {
            $data['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round($averageRating, 2),
                'reviewCount' => $reviewsCount,
            ];
        }

        return $data;
    }

    private function navigation(string $active = 'home'): array
    {
        return [
            ['label' => 'Home', 'url' => url('/'), 'active' => $active === 'home'],
            ['label' => 'Shop', 'url' => url('/shop'), 'active' => $active === 'shop'],
            ['label' => 'Categories', 'url' => url('/categories'), 'active' => $active === 'categories'],
            ['label' => 'Custom Gifts', 'url' => url('/custom-gifts'), 'active' => $active === 'custom-gifts'],
            ['label' => 'About', 'url' => url('/about'), 'active' => $active === 'about'],
            ['label' => 'Contact', 'url' => url('/contact'), 'active' => $active === 'contact'],
            ['label' => Auth::isCustomer() ? 'My Portal' : 'Login', 'url' => Auth::isCustomer() ? url('/customer/dashboard') : url('/login'), 'active' => $active === 'customer'],
        ];
    }
}
