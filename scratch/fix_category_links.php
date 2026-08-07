<?php
require dirname(__DIR__) . '/public/index.php';
use App\Core\Database;
$pdo = Database::connection();
$categories = $pdo->query("SELECT id, slug FROM categories")->fetchAll(PDO::FETCH_ASSOC);
foreach ($categories as $cat) {
    $pdo->prepare("UPDATE categories SET link_url = ? WHERE id = ?")->execute([
        '/shop?category=' . $cat['slug'],
        $cat['id']
    ]);
}
echo "Updated " . count($categories) . " categories with link_url.\n";
