<?php
declare(strict_types=1);
namespace App\Controllers\Admin;
use App\Core\Controller;
use App\Core\Database;
use PDO;

class CtaController extends Controller
{
    public function index(): void
    {
        $pdo=Database::connection();
        $_SESSION['csrf_token']??=bin2hex(random_bytes(32));
        if($_SERVER['REQUEST_METHOD']==='POST') $this->handle($pdo);
        $ctas=$pdo->query('SELECT * FROM homepage_ctas ORDER BY sort_order,id')->fetchAll();
        $editId=filter_input(INPUT_GET,'edit',FILTER_VALIDATE_INT);
        $cta=null;
        if($editId){$s=$pdo->prepare('SELECT * FROM homepage_ctas WHERE id=?');$s->execute([$editId]);$cta=$s->fetch()?:null;}
        $flash=$_SESSION['cta_flash']??null;unset($_SESSION['cta_flash']);
        $content=$this->render('admin/cta/index',compact('ctas','cta','flash')+['csrfToken'=>$_SESSION['csrf_token']]);
        $this->view('layouts/admin-layout',['title'=>'Page CTAs','pageTitle'=>'Page CTAs','showPageTitle'=>false,'content'=>$content]);
    }
    private function handle(PDO $pdo): never
    {
        if(!hash_equals((string)($_SESSION['csrf_token']??''),(string)($_POST['csrf_token']??''))){http_response_code(419);exit('Invalid request token.');}
        $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT)?:null;
        if(($_POST['action']??'save')==='delete'&&$id){$pdo->prepare('DELETE FROM homepage_ctas WHERE id=?')->execute([$id]);$this->redirect('CTA deleted.');}
        $placements=['home_after_categories','home_between_products_combos','about','contact'];
        $placement=in_array($_POST['placement']??'', $placements,true)?$_POST['placement']:'home_between_products_combos';
        $title=trim((string)($_POST['title']??''));if($title==='')$this->redirect('CTA title is required.','error',$id);
        $image='';if($id){$s=$pdo->prepare('SELECT image_path FROM homepage_ctas WHERE id=?');$s->execute([$id]);$image=(string)($s->fetchColumn()?:'');}
        try{$image=$this->upload($_FILES['cta_image']??null)??$image;}catch(\RuntimeException $e){$this->redirect($e->getMessage(),'error',$id);}
        if($image==='')$this->redirect('Background image is required.','error',$id);
        $color=strtoupper(trim((string)($_POST['background_color']??'#0B182E')));if(!preg_match('/^#[0-9A-F]{6}$/',$color))$color='#0B182E';
        $data=[$placement,trim((string)($_POST['badge']??'')),$title,trim((string)($_POST['description']??'')),trim((string)($_POST['button_label']??'')),trim((string)($_POST['link_url']??'')),$image,$color,max(0,min(90,(int)($_POST['overlay_opacity']??55))),(int)($_POST['sort_order']??0),isset($_POST['status'])?'active':'inactive'];
        if($id)$pdo->prepare('UPDATE homepage_ctas SET placement=?,badge=?,title=?,description=?,button_label=?,link_url=?,image_path=?,background_color=?,overlay_opacity=?,sort_order=?,status=? WHERE id=?')->execute([...$data,$id]);
        else $pdo->prepare('INSERT INTO homepage_ctas(placement,badge,title,description,button_label,link_url,image_path,background_color,overlay_opacity,sort_order,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)')->execute($data);
        $this->redirect('CTA saved successfully.');
    }
    private function upload(?array $file):?string{if(!$file||($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)return null;if(($file['error']??1)!==UPLOAD_ERR_OK||(int)($file['size']??0)>8*1024*1024)throw new \RuntimeException('Upload an image smaller than 8MB.');$info=@getimagesize((string)$file['tmp_name']);$types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];if(!isset($types[$info['mime']??'']))throw new \RuntimeException('Use JPG, PNG or WebP.');$dir=BASE_PATH.'/public/assets/uploads/cta';if(!is_dir($dir))mkdir($dir,0775,true);$name=bin2hex(random_bytes(16)).'.'.$types[$info['mime']];if(!move_uploaded_file((string)$file['tmp_name'],$dir.'/'.$name))throw new \RuntimeException('CTA image upload failed.');return '/assets/uploads/cta/'.$name;}
    private function redirect(string $message,string $type='success',?int $id=null):never{$_SESSION['cta_flash']=compact('message','type');header('Location: '.app_url('/admin/cta'.($id?'?edit='.$id:'')),true,303);exit;}
}
