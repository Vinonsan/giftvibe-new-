<?php
declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use PDO;

class ComboController extends Controller
{
    public function index(): void
    {
        $pdo = Database::connection();
        $slug = trim((string)($_GET['combo'] ?? ''));

        if ($slug !== '') {
            $s = $pdo->prepare("SELECT c.*, ci.image_path FROM combos c LEFT JOIN combo_images ci ON ci.combo_id=c.id AND ci.is_primary=1 WHERE c.slug=? AND c.status='active' LIMIT 1");
            $s->execute([$slug]);
            $combo = $s->fetch();

            if ($combo) {
                $s = $pdo->prepare('SELECT * FROM combo_images WHERE combo_id=? ORDER BY is_primary DESC,sort_order,id');
                $s->execute([$combo['id']]);
                $gallery = $s->fetchAll();

                $s = $pdo->prepare('SELECT video_url FROM combo_videos WHERE combo_id=? ORDER BY sort_order,id');
                $s->execute([$combo['id']]);
                $comboVideos = $s->fetchAll(PDO::FETCH_COLUMN);

                $s = $pdo->prepare("SELECT p.*,pi.image_path,GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') category_names FROM combo_products cp JOIN products p ON p.id=cp.product_id LEFT JOIN product_images pi ON pi.product_id=p.id AND pi.is_primary=1 LEFT JOIN product_categories pc ON pc.product_id=p.id LEFT JOIN categories c ON c.id=pc.category_id WHERE cp.combo_id=? AND p.status='active' GROUP BY p.id,cp.sort_order ORDER BY cp.sort_order");
                $s->execute([$combo['id']]);
                $comboProducts = $s->fetchAll();

                $galleryPaths = array_column($gallery, 'image_path');
                foreach ($comboProducts as $comboProduct) {
                    $productImage = (string)($comboProduct['image_path'] ?? '');
                    if ($productImage !== '' && !in_array($productImage, $galleryPaths, true)) {
                        $gallery[] = ['image_path' => $productImage, 'alt_text' => $comboProduct['name'], 'is_primary' => 0];
                        $galleryPaths[] = $productImage;
                    }
                }

                $this->view('layouts/public-layout', [
                    'title' => $combo['name'],
                    'metaDescription' => $combo['description'] ?: 'GiftVibe gift combo.',
                    'canonicalPath' => '/combos?combo=' . rawurlencode($slug),
                    'ogImage' => $combo['image_path'],
                    'structuredData' => [
                        '@context' => 'https://schema.org',
                        '@type' => 'Product',
                        'name' => $combo['name'],
                        'description' => $combo['description'],
                        'image' => array_values($galleryPaths),
                        'productID' => 'combo-' . (int)$combo['id'],
                        'offers' => [
                            '@type' => 'Offer',
                            'priceCurrency' => 'LKR',
                            'price' => (string)$combo['price'],
                            'availability' => 'https://schema.org/InStock'
                        ]
                    ],
                    'content' => $this->render('public/combo/show', compact('combo', 'gallery', 'comboVideos', 'comboProducts'))
                ]);
                return;
            }
        }

        // Listing Filters
        $filters = [
            'search' => trim((string)($_GET['search'] ?? '')),
            'min_price' => max(0, (float)($_GET['min_price'] ?? 0)),
            'max_price' => max(0, (float)($_GET['max_price'] ?? 0))
        ];

        $where = ["c.status='active'"];
        $params = [];

        if ($filters['search'] !== '') {
            $where[] = "(c.name LIKE ? OR c.description LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if ($filters['min_price'] > 0) {
            $where[] = "c.price >= ?";
            $params[] = $filters['min_price'];
        }
        if ($filters['max_price'] > 0) {
            $where[] = "c.price <= ?";
            $params[] = $filters['max_price'];
        }

        $whereSql = implode(' AND ', $where);

        $stmt = $pdo->prepare("SELECT c.*, ci.image_path, COUNT(cp.product_id) product_count 
            FROM combos c 
            LEFT JOIN combo_images ci ON ci.combo_id = c.id AND ci.is_primary = 1 
            LEFT JOIN combo_products cp ON cp.combo_id = c.id 
            WHERE {$whereSql} 
            GROUP BY c.id 
            ORDER BY c.id DESC");
        $stmt->execute($params);
        $combos = $stmt->fetchAll();

        $priceBounds = $pdo->query("SELECT COALESCE(MIN(price),0) min_price, COALESCE(MAX(price),0) max_price FROM combos WHERE status='active'")->fetch();

        $this->view('layouts/public-layout', [
            'title' => 'Gift Combos',
            'metaDescription' => 'Shop curated GiftVibe product combos.',
            'canonicalPath' => '/combos',
            'ogImage' => '/assets/images/combo-showcase-giftvibe.jpg',
            'content' => $this->render('public/combo/index', compact('combos', 'filters', 'priceBounds'))
        ]);
    }
}
