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
        $categorySlug = trim((string) ($_GET['category'] ?? ''));
        $categories = $pdo->query("SELECT id,name,slug FROM categories WHERE status='active' ORDER BY sort_order,id")->fetchAll();
        $sql = "SELECT p.*,pi.image_path,GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') category_names FROM products p LEFT JOIN product_images pi ON pi.product_id=p.id AND pi.is_primary=1 LEFT JOIN product_categories pc ON pc.product_id=p.id LEFT JOIN categories c ON c.id=pc.category_id WHERE p.status='active'";
        $params = [];
        if ($categorySlug !== '') {
            $sql .= ' AND EXISTS (SELECT 1 FROM product_categories pcf JOIN categories cf ON cf.id=pcf.category_id WHERE pcf.product_id=p.id AND cf.slug=?)';
            $params[] = $categorySlug;
        }
        $sql .= ' GROUP BY p.id ORDER BY p.is_featured DESC,p.id DESC';
        $statement = $pdo->prepare($sql); $statement->execute($params);
        $products = $statement->fetchAll();
        $activeName = 'All gifts';
        foreach ($categories as $category) if ($category['slug'] === $categorySlug) $activeName = $category['name'];

        $this->view('layouts/public-layout', [
            'title' => $activeName,
            'metaDescription' => 'Shop thoughtful gifts from GiftVibe.',
            'canonicalPath' => '/shop',
            'content' => $this->render('public/shop/index', compact('products', 'categories', 'categorySlug', 'activeName')),
        ]);
    }
}
