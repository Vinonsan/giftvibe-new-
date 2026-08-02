<?php
declare(strict_types=1);namespace App\Controllers\Admin;
use App\Core\Controller;
class PlaceholderController extends Controller{
 public function orders():void{$this->show('Orders','Order management is not configured yet.');}
 public function customers():void{$this->show('Customers','Customer management is not configured yet.');}
 public function messages():void{$this->show('Messages','Message management is not configured yet.');}
 public function settings():void{$this->show('Settings','Site settings are not configured yet.');}
 private function show(string $title,string $description):void{$this->view('layouts/admin-layout',['title'=>$title,'showPageTitle'=>false,'content'=>$this->render('admin/placeholder/index',compact('title','description'))]);}
}
