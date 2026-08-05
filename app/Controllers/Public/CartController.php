<?php
declare(strict_types=1);namespace App\Controllers\Public;
use App\Core\Controller;
class CartController extends Controller{public function index():void{$this->view('layouts/public-layout',['title'=>'Your Cart','metaDescription'=>'Review gifts added to your GiftVibe shopping cart.','canonicalPath'=>'/cart','robots'=>'noindex, follow','content'=>$this->render('public/cart/index')]);}}
