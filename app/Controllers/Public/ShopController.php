<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;

class ShopController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        
        // Check if viewing a specific product
        $productSlug = trim((string) ($_GET['product'] ?? ''));
        $categories = $pdo->query("SELECT id,name,slug FROM categories WHERE status='active' ORDER BY sort_order,id")->fetchAll();
        
        if ($productSlug !== '') {
            $statement = $pdo->prepare("SELECT p.*, pi.image_path, GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names FROM products p LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1 LEFT JOIN product_categories pc ON pc.product_id = p.id LEFT JOIN categories c ON c.id = pc.category_id WHERE p.slug = ? AND p.status = 'active' GROUP BY p.id LIMIT 1");
            $statement->execute([$productSlug]);
            $product = $statement->fetch();
            
            if ($product) {
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
                $siteUrl = rtrim((string) (getenv('APP_URL') ?: 'https://giftvibelk.lk'), '/');
                $productPath = '/shop?product=' . rawurlencode($productSlug);
                $seoImage = (string) ($product['image_path'] ?? '/assets/images/hero_slide_1.jpg');
                if (str_starts_with($seoImage, 'public/')) $seoImage = '/' . substr($seoImage, 7);
                if (!str_starts_with($seoImage, 'http')) $seoImage = $siteUrl . '/' . ltrim($seoImage, '/');
                // Fetch product gallery images
                $galleryStmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, id");
                $galleryStmt->execute([$product['id']]);
                $gallery = $galleryStmt->fetchAll();
                $seoImages = [$seoImage];
                foreach ($gallery as $galleryImage) {
                    $galleryPath = (string) ($galleryImage['image_path'] ?? '');
                    if ($galleryPath === '') continue;
                    if (str_starts_with($galleryPath, 'public/')) $galleryPath = '/' . substr($galleryPath, 7);
                    if (!str_starts_with($galleryPath, 'http')) $galleryPath = $siteUrl . '/' . ltrim($galleryPath, '/');
                    if (!in_array($galleryPath, $seoImages, true)) $seoImages[] = $galleryPath;
                }
                $videoStmt = $pdo->prepare('SELECT video_url FROM product_videos WHERE product_id=? ORDER BY sort_order,id');
                $videoStmt->execute([$product['id']]);
                $productVideos = $videoStmt->fetchAll(\PDO::FETCH_COLUMN);
                if (!$productVideos && trim((string) ($product['video_url'] ?? '')) !== '') $productVideos[] = $product['video_url'];
                $variantStmt = $pdo->prepare("SELECT id,name,sku,color_name,color_hex,image_path,price_adjustment,stock_quantity FROM product_variants WHERE product_id=? AND status='active' ORDER BY id");
                $variantStmt->execute([$product['id']]);
                $productVariants = $variantStmt->fetchAll(\PDO::FETCH_ASSOC);

                // Fetch category IDs for the current product to query related items
                $catIdsStmt = $pdo->prepare("SELECT category_id FROM product_categories WHERE product_id = ?");
                $catIdsStmt->execute([$product['id']]);
                $categoryIds = $catIdsStmt->fetchAll(\PDO::FETCH_COLUMN);

                $relatedProducts = [];
                if ($categoryIds) {
                    $inClause = implode(',', array_fill(0, count($categoryIds), '?'));
                    $relatedSql = "SELECT p.*, pi.image_path, GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names FROM products p LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1 LEFT JOIN product_categories pc ON pc.product_id = p.id LEFT JOIN categories c ON c.id = pc.category_id WHERE pc.category_id IN ($inClause) AND p.id != ? AND p.status = 'active' GROUP BY p.id ORDER BY p.is_featured DESC, p.id DESC LIMIT 4";
                    $relatedStmt = $pdo->prepare($relatedSql);
                    $relatedStmt->execute(array_merge($categoryIds, [$product['id']]));
                    $relatedProducts = $relatedStmt->fetchAll();
                }

                $this->view('layouts/public-layout', [
                    'title' => $product['meta_title'] ?: $product['name'],
                    'metaDescription' => $product['meta_description'] ?: ($product['short_description'] ?: $product['name'] . ' from GiftVibe.'),
                    'canonicalPath' => $productPath,
                    'ogImage' => (string) ($product['image_path'] ?? '/assets/images/hero_slide_1.jpg'),
                    'ogType' => 'product',
                    'structuredData' => [
                        '@context' => 'https://schema.org',
                        '@graph' => [
                            ['@type' => 'Product', '@id' => $siteUrl . $productPath . '#product', 'name' => $product['name'], 'description' => $product['meta_description'] ?: ($product['short_description'] ?: $product['description']), 'image' => $seoImages, 'sku' => $product['sku'], 'productID' => (string) $product['id'], 'category' => $product['category_names'], 'brand' => ['@type'=>'Brand','name'=>'GiftVibe'], 'url' => $siteUrl . $productPath, 'mainEntityOfPage' => $siteUrl . $productPath, 'offers' => ['@type'=>'Offer','priceCurrency'=>'LKR','price'=>(string)$product['base_price'],'availability'=>(int)$product['stock_quantity']>0?'https://schema.org/InStock':'https://schema.org/OutOfStock','url'=>$siteUrl.$productPath,'seller'=>['@type'=>'Organization','name'=>'GiftVibe']]],
                            ['@type' => 'BreadcrumbList', 'itemListElement' => [
                                ['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>$siteUrl.'/'],
                                ['@type'=>'ListItem','position'=>2,'name'=>'Shop','item'=>$siteUrl.'/shop'],
                                ['@type'=>'ListItem','position'=>3,'name'=>$product['name'],'item'=>$siteUrl.$productPath],
                            ]],
                        ],
                    ],
                    'content' => $this->render('public/product/show', compact('product', 'gallery', 'relatedProducts', 'categories', 'productVideos', 'productVariants')),
                ]);
                return;
            }
        }
        
        // Fallback: Product Listing Grid
        $filters = $this->filters();
        $categorySlug = $filters['category'];
        [$products, $totalProducts] = $this->filteredProducts($pdo, $filters, 0, 20);
        $priceBounds = $pdo->query("SELECT COALESCE(MIN(base_price),0) min_price,COALESCE(MAX(base_price),0) max_price FROM products WHERE status='active'")->fetch();
        
        $activeName = 'All gifts';
        foreach ($categories as $category) {
            if ($category['slug'] === $categorySlug) {
                $activeName = $category['name'];
            }
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?: 'localhost';
        $siteUrl = rtrim((string) (getenv('APP_URL') ?: 'https://giftvibelk.lk'), '/');
        $categoryCanonical = $categorySlug !== '' ? '/shop?category=' . rawurlencode($categorySlug) : '/shop';
        $itemList = [];
        foreach ($products as $index => $listedProduct) {
            $itemList[] = ['@type'=>'ListItem','position'=>$index + 1,'url'=>$siteUrl.'/shop?product='.rawurlencode((string)$listedProduct['slug']),'name'=>$listedProduct['name']];
        }

        $shopTitle = $categorySlug !== '' ? $activeName . ' - Shop Curated Gifts' : 'Shop Premium Gift Hampers & Curated Celebration Boxes';
        $shopDescription = $categorySlug !== '' ? 'Explore our curated ' . $activeName . ' hampers. Hand-delivered across Sri Lanka, including Jaffna.' : 'Explore our collection of custom gift hampers, birthday treats, flowers, and surprise packages. Handcrafted and delivered across Sri Lanka, including Colombo and Jaffna.';

        $this->view('layouts/public-layout', [
            'title' => $shopTitle,
            'metaDescription' => $shopDescription,
            'canonicalPath' => $categoryCanonical,
            'ogImage' => '/assets/images/hero_slide_1.jpg',
            'structuredData' => ['@context'=>'https://schema.org','@type'=>'ItemList','name'=>$activeName,'url'=>$siteUrl.$categoryCanonical,'numberOfItems'=>count($itemList),'itemListElement'=>$itemList],
            'content' => $this->render('public/shop/index', compact('products', 'categories', 'categorySlug', 'activeName', 'filters', 'totalProducts', 'priceBounds')),
        ]);
    }

    public function category(): void
    {
        $slug = trim((string) ($_GET['slug'] ?? $this->param('slug') ?? ''));
        $pdo  = Database::connection();

        /* Load the category */
        $catStmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? AND status = 'active' LIMIT 1");
        $catStmt->execute([$slug]);
        $category = $catStmt->fetch();

        /* 404 if slug not found */
        if (!$category && $slug !== '') {
            http_response_code(404);
        }

        /* Load real products for this category */
        $products = [];
        if ($category) {
            $pStmt = $pdo->prepare(
                "SELECT p.*, pi.image_path,
                        GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names
                 FROM products p
                 LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
                 LEFT JOIN product_categories pc ON pc.product_id = p.id
                 LEFT JOIN categories c ON c.id = pc.category_id
                 WHERE pc.category_id = ? AND p.status = 'active'
                 GROUP BY p.id
                 ORDER BY p.is_featured DESC, p.id DESC"
            );
            $pStmt->execute([(int) $category['id']]);
            $products = $pStmt->fetchAll();
        }

        /* Mock products shown when DB has no real data yet */
        if (empty($products)) {
            $mockImages = [
                '/assets/images/hero_slide_1.jpg',
                '/assets/images/hero_slide_2.jpg',
                '/assets/images/hero_slide_3.jpg',
            ];
            $mockNames = [
                'Rose Bouquet Premium', 'Birthday Gift Hamper', 'Chocolate Surprise Box',
                'Flower & Cake Combo', 'Romantic Candle Set', 'Anniversary Luxury Pack',
            ];
            foreach ($mockNames as $i => $name) {
                $products[] = [
                    'id'             => $i + 1,
                    'name'           => $name,
                    'slug'           => 'mock-' . ($i + 1),
                    'short_description' => 'A beautifully curated gift, hand-delivered across Sri Lanka.',
                    'base_price'     => 2490 + ($i * 500),
                    'sale_price'     => null,
                    'image_path'     => $mockImages[$i % 3],
                    'stock_quantity' => 10,
                    'is_featured'    => $i < 2 ? 1 : 0,
                    'status'         => 'active',
                    'category_names' => $category['name'] ?? 'Gift',
                ];
            }
        }

        $categoryName = (string) ($category['name'] ?? ucwords(str_replace('-', ' ', $slug)));
        $siteUrl      = rtrim((string) (getenv('APP_URL') ?: 'https://giftvibelk.lk'), '/');
        $canonicalPath = '/shop/category/' . rawurlencode($slug);

        $this->view('layouts/public-layout', [
            'title'           => $categoryName . ' — Curated Gifts | GiftVibe',
            'metaDescription' => 'Shop ' . $categoryName . ' gifts hand-delivered across Sri Lanka. Find the perfect gift hamper at GiftVibe.',
            'canonicalPath'   => $canonicalPath,
            'ogImage'         => (string) ($category['image_path'] ?? '/assets/images/hero_slide_1.jpg'),
            'content'         => $this->render('public/shop/category', compact('category', 'products', 'categoryName', 'slug')),
        ]);
    }

    public function products(): void
    {
        header('Content-Type: application/json');
        $pdo = Database::connection();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        [$products, $total] = $this->filteredProducts($pdo, $this->filters(), ($page - 1) * 20, 20);
        if (!defined('PRODUCT_CARD_JS_DEFINED')) define('PRODUCT_CARD_JS_DEFINED', true);
        $html = '';
        foreach ($products as $product) $html .= $this->render('components/base/product-card', compact('product'));
        echo json_encode(['html'=>$html,'count'=>count($products),'total'=>$total,'has_more'=>$page*20<$total], JSON_UNESCAPED_SLASHES);
    }

    private function filters(): array
    {
        return ['category'=>trim((string)($_GET['category']??'')),'search'=>trim((string)($_GET['search']??'')),'min_price'=>max(0,(float)($_GET['min_price']??0)),'max_price'=>max(0,(float)($_GET['max_price']??0))];
    }

    private function filteredProducts(\PDO $pdo, array $filters, int $offset, int $limit): array
    {
        $where = ["p.status='active'"]; $params = [];
        if ($filters['category'] !== '') { $where[]='EXISTS (SELECT 1 FROM product_categories pcf JOIN categories cf ON cf.id=pcf.category_id WHERE pcf.product_id=p.id AND cf.slug=?)'; $params[]=$filters['category']; }
        if ($filters['search'] !== '') { $where[]='(p.name LIKE ? OR p.sku LIKE ? OR p.short_description LIKE ?)'; $term='%'.$filters['search'].'%'; array_push($params,$term,$term,$term); }
        if ($filters['min_price'] > 0) { $where[]='p.base_price>=?'; $params[]=$filters['min_price']; }
        if ($filters['max_price'] > 0) { $where[]='p.base_price<=?'; $params[]=$filters['max_price']; }
        $whereSql=implode(' AND ',$where);
        $count=$pdo->prepare("SELECT COUNT(*) FROM products p WHERE {$whereSql}");$count->execute($params);$total=(int)$count->fetchColumn();
        $sql="SELECT p.*,pi.image_path,GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') category_names FROM products p LEFT JOIN product_images pi ON pi.product_id=p.id AND pi.is_primary=1 LEFT JOIN product_categories pc ON pc.product_id=p.id LEFT JOIN categories c ON c.id=pc.category_id WHERE {$whereSql} GROUP BY p.id ORDER BY p.is_featured DESC,p.id DESC LIMIT ".max(1,$limit).' OFFSET '.max(0,$offset);
        $stmt=$pdo->prepare($sql);$stmt->execute($params);return [$stmt->fetchAll(),$total];
    }
}
