<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;

class HomeController extends Controller
{
    public function index(): void
    {
        try {
            $statement = Database::connection()->query("SELECT * FROM banners WHERE placement = 'hero' AND status = 'active' ORDER BY sort_order, id");
            $banners = $statement->fetchAll();
        } catch (\Throwable) {
            $banners = [];
        }

        if (!$banners) {
            $banners = $this->fallbackBanners();
        }

        $primarySlide = $banners[0];
        $parts = explode('|', (string) ($primarySlide['subtitle'] ?? ''));
        $description = trim((string) ($parts[1] ?? '')) ?: 'Discover thoughtful gifts, curated gift boxes, flowers and sweet hampers from GiftVibe.';

        $this->view('layouts/public-layout', [
            'title' => (string) $primarySlide['title'],
            'metaDescription' => $description,
            'canonicalPath' => '/',
            'ogImage' => (string) ($primarySlide['image_path'] ?? '/assets/images/hero_slide_1.jpg'),
            'structuredData' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'GiftVibe',
                'url' => '/',
                'description' => $description,
            ],
            'content' => $this->render('public/home/index', [
                'title' => 'Home',
                'message' => 'Welcome to Gift Vibe',
                'heroSlides' => $banners,
            ]),
        ]);
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
