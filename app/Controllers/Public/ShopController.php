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
                $siteUrl = rtrim((string) (getenv('APP_URL') ?: $scheme . '://' . $host), '/');
                $productPath = '/shop?product=' . rawurlencode($productSlug);
                $seoImage = (string) ($product['image_path'] ?? '/assets/images/hero_slide_1.jpg');
                if (str_starts_with($seoImage, 'public/')) $seoImage = '/' . substr($seoImage, 7);
                if (!str_starts_with($seoImage, 'http')) $seoImage = $siteUrl . '/' . ltrim($seoImage, '/');
                // Fetch product gallery images
                $galleryStmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, id");
                $galleryStmt->execute([$product['id']]);
                $gallery = $galleryStmt->fetchAll();
                $videoStmt = $pdo->prepare('SELECT video_url FROM product_videos WHERE product_id=? ORDER BY sort_order,id');
                $videoStmt->execute([$product['id']]);
                $productVideos = $videoStmt->fetchAll(\PDO::FETCH_COLUMN);
                if (!$productVideos && trim((string) ($product['video_url'] ?? '')) !== '') $productVideos[] = $product['video_url'];

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
                    'title' => $product['name'],
                    'metaDescription' => $product['short_description'] ?: $product['name'] . ' from GiftVibe.',
                    'canonicalPath' => $productPath,
                    'ogImage' => (string) ($product['image_path'] ?? '/assets/images/hero_slide_1.jpg'),
                    'structuredData' => [
                        '@context' => 'https://schema.org', '@type' => 'Product',
                        'name' => $product['name'], 'description' => $product['short_description'] ?: $product['description'],
                        'image' => [$seoImage], 'sku' => $product['sku'], 'productID' => (string) $product['id'],
                        'offers' => ['@type'=>'Offer','priceCurrency'=>'LKR','price'=>(string)$product['base_price'],'availability'=>(int)$product['stock_quantity']>0?'https://schema.org/InStock':'https://schema.org/OutOfStock','url'=>$siteUrl.$productPath],
                    ],
                    'content' => $this->render('public/product/show', compact('product', 'gallery', 'relatedProducts', 'categories', 'productVideos')),
                ]);
                return;
            }
        }
        
        // Fallback: Product Listing Grid
        $categorySlug = trim((string) ($_GET['category'] ?? ''));
        $sql = "SELECT p.*, pi.image_path, GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names FROM products p LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1 LEFT JOIN product_categories pc ON pc.product_id = p.id LEFT JOIN categories c ON c.id = pc.category_id WHERE p.status = 'active'";
        $params = [];
        if ($categorySlug !== '') {
            $sql .= ' AND EXISTS (SELECT 1 FROM product_categories pcf JOIN categories cf ON cf.id=pcf.category_id WHERE pcf.product_id=p.id AND cf.slug=?)';
            $params[] = $categorySlug;
        }
        $sql .= ' GROUP BY p.id ORDER BY p.is_featured DESC, p.id DESC';
        $statement = $pdo->prepare($sql);
        $statement->execute($params);
        $products = $statement->fetchAll();
        
        $activeName = 'All gifts';
        foreach ($categories as $category) {
            if ($category['slug'] === $categorySlug) {
                $activeName = $category['name'];
            }
        }

        $this->view('layouts/public-layout', [
            'title' => $activeName,
            'metaDescription' => 'Shop thoughtful gifts from GiftVibe.',
            'canonicalPath' => '/shop',
            'content' => $this->render('public/shop/index', compact('products', 'categories', 'categorySlug', 'activeName')),
        ]);
    }
}
