<?php
require dirname(__DIR__) . '/public/index.php';

use App\Core\Database;
$pdo = Database::connection();

$categories = $pdo->query("SELECT id FROM categories")->fetchAll(\PDO::FETCH_COLUMN);

if (empty($categories)) {
    echo "No categories found to attach mock data to.\n";
    exit;
}

$mockProducts = [
    ['Rose Bouquet Premium', 'beautiful-rose-bouquet', 4500, '/assets/images/hero_slide_1.jpg', 'A premium bouquet of fresh red roses.'],
    ['Birthday Gift Hamper', 'birthday-gift-hamper', 6500, '/assets/images/hero_slide_2.jpg', 'The perfect hamper for birthdays with cake and chocolates.'],
    ['Chocolate Surprise Box', 'chocolate-surprise-box', 3200, '/assets/images/hero_slide_3.jpg', 'An assortment of fine chocolates in a surprise box.'],
    ['Flower & Cake Combo', 'flower-cake-combo', 5500, '/assets/images/hero_slide_1.jpg', 'Delicious cake paired with stunning seasonal flowers.'],
    ['Romantic Candle Set', 'romantic-candle-set', 2800, '/assets/images/hero_slide_2.jpg', 'Set the mood with these scented romantic candles.'],
    ['Anniversary Luxury Pack', 'anniversary-luxury-pack', 8900, '/assets/images/hero_slide_3.jpg', 'Luxury curated pack for celebrating anniversaries.'],
];

foreach ($mockProducts as $i => $p) {
    // Check if slug exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
    $stmt->execute([$p[1]]);
    if ($stmt->fetch()) {
        echo "Product {$p[1]} already exists, skipping.\n";
        continue;
    }

    // Insert Product
    $stmt = $pdo->prepare("INSERT INTO products (sku, name, slug, short_description, description, base_price, stock_quantity, status, is_featured) VALUES (?, ?, ?, ?, ?, ?, 10, 'active', 1)");
    $sku = 'MOCK-' . str_pad((string)($i + 1), 4, '0', STR_PAD_LEFT);
    $stmt->execute([
        $sku,
        $p[0],
        $p[1],
        $p[4],
        $p[4],
        $p[2]
    ]);
    
    $productId = $pdo->lastInsertId();

    // Add Image
    $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_primary) VALUES (?, ?, 1)");
    $stmt->execute([$productId, $p[3]]);

    // Add to random category
    $randomCatId = $categories[array_rand($categories)];
    $stmt = $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
    $stmt->execute([$productId, $randomCatId]);
    
    echo "Added mock product: {$p[0]} (SKU: {$sku})\n";
}

echo "Done.\n";
