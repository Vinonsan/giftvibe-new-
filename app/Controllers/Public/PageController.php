<?php
declare(strict_types=1);
namespace App\Controllers\Public;
use App\Core\Controller;
use App\Core\Database;

class PageController extends Controller
{
    public function about(): void { $this->show('about', 'About', 'Learn about GiftVibe, our story, and our passion for bringing people together. Discover how we curate premium gift boxes, flower bouquets, and custom presents.'); }
    public function services(): void { $this->show('services', 'Services', 'Explore our premium gifting services including customized gift hampers, corporate gifts, floral arrangements, and fast home delivery across Colombo & Sri Lanka.'); }
    public function blog(): void { $this->show('blog', 'Blog', 'Discover thoughtful gift ideas, celebration guides, and tips for choosing the perfect present for birthdays, anniversaries, and holidays in Sri Lanka.'); }
    public function contact(): void { $this->show('contact', 'Contact', 'Get in touch with GiftVibe. Contact us for custom gift requests, order tracking, corporate hampers, and general inquiries. We are here to help you.'); }
    public function privacy(): void { $this->legal('Privacy Policy', 'privacy', 'How GiftVibe collects, uses and protects information shared with us.'); }
    public function terms(): void { $this->legal('Terms & Conditions', 'terms', 'The terms that apply when browsing GiftVibe and arranging an order.'); }
    private function show(string $page, string $title, string $intro): void
    {
        $cta = null;
        $faqs = [];
        try {
            $pdo = Database::connection();
            $s = $pdo->prepare("SELECT * FROM homepage_ctas WHERE placement=? AND status='active' ORDER BY sort_order,id DESC LIMIT 1");
            $s->execute([$page]);
            $cta = $s->fetch() ?: null;

            $s = $pdo->prepare("SELECT * FROM faqs WHERE status='active' AND category=? ORDER BY sort_order,id LIMIT 10");
            $s->execute([$page]);
            $faqs = $s->fetchAll();
        } catch (\Throwable $e) {}

        $viewPath = $page === 'about' ? 'public/about/index' : 'public/pages/' . $page;
        $content = $this->render($viewPath, compact('title','intro','cta','faqs'));
        $siteUrl = rtrim((string)(getenv('APP_URL')?:'https://giftvibelk.lk'),'/');
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $page === 'contact' ? 'ContactPage' : ($page === 'about' ? 'AboutPage' : ($page === 'blog' ? 'Blog' : 'WebPage')),
            'name' => $title,
            'description' => $intro,
            'url' => $siteUrl.'/'.$page,
            'isPartOf' => ['@type' => 'WebSite', 'name' => 'GiftVibe LK', 'url' => $siteUrl.'/']
        ];

        $structuredData = [$schema];

        /* Services page gets a dedicated Service schema for rich SEO results. */
        if ($page === 'services') {
            $structuredData[] = [
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'serviceType' => 'Gift delivery and personalisation',
                'name' => 'GiftVibe Gifting Services',
                'description' => $intro,
                'provider' => ['@type' => 'LocalBusiness', 'name' => 'GiftVibe LK', 'url' => $siteUrl.'/'],
                'url' => $siteUrl.'/services',
                'areaServed' => [
                    ['@type' => 'City', 'name' => 'Colombo'],
                    ['@type' => 'City', 'name' => 'Jaffna'],
                    ['@type' => 'City', 'name' => 'Kandy'],
                    ['@type' => 'City', 'name' => 'Galle'],
                    ['@type' => 'Country', 'name' => 'Sri Lanka'],
                ],
                'hasOfferCatalog' => [
                    '@type' => 'OfferCatalog',
                    'name' => 'GiftVibe services',
                    'itemListElement' => [
                        ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Personalised gifting']],
                        ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Curated gift boxes']],
                        ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Flowers and sweet treats']],
                        ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Gift combo delivery']],
                        ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Corporate gifting']],
                        ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Gifting guidance']],
                    ],
                ],
            ];
        }

        if (!empty($faqs)) {
            $structuredData[] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn($faq) => [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer']
                    ]
                ], $faqs)
            ];
        }

        $pageImages = [
            'about' => '/assets/images/hero_slide_2.jpg',
            'services' => '/assets/images/product-showcase-giftvibe.jpg',
            'contact' => '/assets/images/hero_slide_3.jpg',
            'blog' => '/assets/images/hero_slide_1.jpg'
        ];
        $ogImage = $pageImages[$page] ?? '/assets/images/giftvibe-mark.svg';

        $this->view('layouts/public-layout', [
            'title' => $title,
            'metaDescription' => $intro,
            'canonicalPath' => '/'.$page,
            'ogImage' => $ogImage,
            'structuredData' => $structuredData,
            'content' => $content
        ]);
    }
    private function legal(string $title,string $page,string $intro):void
    {
        $content=$this->render('public/pages/legal',compact('title','page','intro'));
        $this->view('layouts/public-layout',['title'=>$title,'metaDescription'=>$intro,'canonicalPath'=>'/'.$page,'content'=>$content]);
    }
}
