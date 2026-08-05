<?php
declare(strict_types=1);
namespace App\Controllers\Public;
use App\Core\Controller;
use App\Core\Database;

class PageController extends Controller
{
    public function about(): void { $this->show('about', 'About GiftVibe', 'Thoughtful gifting, made personal.'); }
    public function services(): void { $this->show('services', 'Gift Delivery & Gifting Services in Sri Lanka', 'Personalised gifts, curated gift boxes, flowers, celebration combos and thoughtful delivery services across Sri Lanka.'); }
    public function blog(): void { $this->show('blog', 'Gift Ideas & Celebration Guides', 'Helpful gift ideas, occasion inspiration and practical guides for choosing meaningful gifts in Sri Lanka.'); }
    public function contact(): void { $this->show('contact', 'Contact GiftVibe', 'We are here to help you choose the perfect gift.'); }
    public function privacy(): void { $this->legal('Privacy Policy', 'privacy', 'How GiftVibe collects, uses and protects information shared with us.'); }
    public function terms(): void { $this->legal('Terms & Conditions', 'terms', 'The terms that apply when browsing GiftVibe and arranging an order.'); }
    private function show(string $page, string $title, string $intro): void
    {
        $cta = null;
        try { $s=Database::connection()->prepare("SELECT * FROM homepage_ctas WHERE placement=? AND status='active' ORDER BY sort_order,id DESC LIMIT 1");$s->execute([$page]);$cta=$s->fetch()?:null; } catch (\Throwable) {}
        $content=$this->render('public/pages/'.$page, compact('title','intro','cta'));
        $siteUrl=rtrim((string)(getenv('APP_URL')?:'https://giftvibelk.lk'),'/');
        $schema=['@context'=>'https://schema.org','@type'=>$page==='contact'?'ContactPage':($page==='about'?'AboutPage':($page==='blog'?'Blog':'WebPage')),'name'=>$title,'description'=>$intro,'url'=>$siteUrl.'/'.$page,'isPartOf'=>['@type'=>'WebSite','name'=>'GiftVibe LK','url'=>$siteUrl.'/']];
        $this->view('layouts/public-layout',['title'=>$title,'metaDescription'=>$intro,'canonicalPath'=>'/'.$page,'structuredData'=>$schema,'content'=>$content]);
    }
    private function legal(string $title,string $page,string $intro):void
    {
        $content=$this->render('public/pages/legal',compact('title','page','intro'));
        $this->view('layouts/public-layout',['title'=>$title,'metaDescription'=>$intro,'canonicalPath'=>'/'.$page,'content'=>$content]);
    }
}
