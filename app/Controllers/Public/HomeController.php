<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
        $categories = [];
        $products = [];
        $combos = [];
        $homepageCtas = [];
        $testimonials = [];
        $faqs = [];
        try {
            $pdo = Database::connection();
            $statement = $pdo->query("SELECT * FROM banners WHERE placement = 'hero' AND status = 'active' ORDER BY sort_order, id");
            $banners = $statement->fetchAll();

            $columns = $pdo->query('DESCRIBE categories')->fetchAll(\PDO::FETCH_COLUMN);
            $statusColumn = in_array('status', $columns, true) ? 'status' : (in_array('is_active', $columns, true) ? 'is_active' : null);
            $orderColumn = in_array('sort_order', $columns, true) ? 'sort_order' : (in_array('display_order', $columns, true) ? 'display_order' : 'id');
            $where = $statusColumn === 'status' ? " WHERE status = 'active'" : ($statusColumn === 'is_active' ? ' WHERE is_active = 1' : '');
            $categories = $pdo->query("SELECT * FROM categories{$where} ORDER BY {$orderColumn}, id")->fetchAll();
            $products = $pdo->query("SELECT p.*, pi.image_path, GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS category_names FROM products p LEFT JOIN product_images pi ON pi.product_id=p.id AND pi.is_primary=1 LEFT JOIN product_categories pc ON pc.product_id=p.id LEFT JOIN categories c ON c.id=pc.category_id WHERE p.status='active' GROUP BY p.id ORDER BY p.is_featured DESC,p.id DESC LIMIT 4")->fetchAll();
            $combos = $pdo->query("SELECT c.*,ci.image_path,COUNT(cp.product_id) product_count FROM combos c LEFT JOIN combo_images ci ON ci.combo_id=c.id AND ci.is_primary=1 LEFT JOIN combo_products cp ON cp.combo_id=c.id WHERE c.status='active' GROUP BY c.id ORDER BY c.id DESC LIMIT 4")->fetchAll();
            $ctaRows = $pdo->query("SELECT * FROM homepage_ctas WHERE status='active' AND placement LIKE 'home_%' ORDER BY sort_order, id")->fetchAll();
            foreach ($ctaRows as $ctaRow) {
                $homepageCtas[$ctaRow['placement']] = $ctaRow;
            }
            $testimonials = $pdo->query("SELECT * FROM testimonials WHERE status='approved' ORDER BY sort_order,id DESC LIMIT 6")->fetchAll();
            $faqs = $pdo->query("SELECT * FROM faqs WHERE status='active' AND category='home' ORDER BY sort_order,id LIMIT 10")->fetchAll();
        } catch (\Throwable) {
            $banners = [];
        }

        if (!$banners) {
            $banners = $this->fallbackBanners();
        }

        $primarySlide = $banners[0];
        $parts = explode('|', (string) ($primarySlide['subtitle'] ?? ''));
        $description = trim((string) ($parts[1] ?? '')) ?: 'Discover thoughtful gifts, curated gift boxes, flowers and sweet hampers from GiftVibe.';
        $siteUrl = rtrim((string) (getenv('APP_URL') ?: 'https://giftvibelk.lk'), '/');
        $averageRating = $testimonials ? round(array_sum(array_map(static fn(array $review): int => (int) $review['rating'], $testimonials)) / count($testimonials), 1) : null;

        $this->view('layouts/public-layout', [
            'title' => (string) $primarySlide['title'],
            'metaDescription' => $description,
            'canonicalPath' => '/',
            'ogImage' => (string) ($primarySlide['image_path'] ?? '/assets/images/hero_slide_1.jpg'),
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@graph' => array_values(array_filter([
                    ['@type'=>'WebSite','name'=>'GiftVibe LK','url'=>$siteUrl.'/','description'=>$description],
                    $faqs ? ['@type'=>'FAQPage','mainEntity'=>array_map(fn($faq)=>['@type'=>'Question','name'=>$faq['question'],'acceptedAnswer'=>['@type'=>'Answer','text'=>$faq['answer']]],$faqs)] : null,
                    $testimonials ? ['@type'=>'Organization','name'=>'GiftVibe LK','url'=>$siteUrl.'/','logo'=>$siteUrl.'/assets/images/giftvibe-mark.svg','aggregateRating'=>['@type'=>'AggregateRating','ratingValue'=>$averageRating,'reviewCount'=>count($testimonials),'bestRating'=>5],'review'=>array_map(fn($review)=>['@type'=>'Review','author'=>['@type'=>'Person','name'=>$review['reviewer_name']],'reviewRating'=>['@type'=>'Rating','ratingValue'=>(int)$review['rating'],'bestRating'=>5],'name'=>$review['title']?:'GiftVibe customer review','reviewBody'=>$review['review_text']],$testimonials)] : null,
                ])),
            ],
            'content' => $this->render('public/home/index', [
                'title' => 'Home',
                'message' => 'Welcome to Gift Vibe',
                'heroSlides' => $banners,
                'categories' => $categories,
                'products' => $products,
                'combos' => $combos,
                'homepageCtas' => $homepageCtas,
                'testimonials' => $testimonials,
                'faqs' => $faqs,
                'reviewFlash' => $_SESSION['review_flash'] ?? null,
                'csrfToken' => $_SESSION['csrf_token'],
            ]),
        ]);
        unset($_SESSION['review_flash']);
    }

    private function fallbackBanners(): array
    {
        return [
            [
                'title' => 'Find the Perfect Gift for Your Loved Ones',
                'subtitle' => 'CRAFTED WITH LOVE|Exquisite curated gift boxes for every special moment|From LKR 4,500.00|EXPLORE NOW|bg-[#0B1528]',
                'image_path' => '/assets/images/hero_slide_1.jpg',
                'link_url' => '/shop',
            ],
            [
                'title' => 'Luxury Bouquets to Brighten Up Their Day',
                'subtitle' => 'FRESH & ELEGANT|Freshly picked luxury flowers delivered with care|From LKR 3,800.00|ORDER FLOWERS|bg-[#111111]',
                'image_path' => '/assets/images/hero_slide_2.jpg',
                'link_url' => '/shop',
            ],
            [
                'title' => 'Assorted Gourmet Chocolates & Sweet Hampers',
                'subtitle' => 'SWEET INDULGENCE|Premium chocolates and hampers for joyful celebrations|From LKR 5,200.00|SHOP SWEETS|bg-[#2C0A1A]',
                'image_path' => '/assets/images/hero_slide_3.jpg',
                'link_url' => '/shop',
            ],
        ];
    }
}
