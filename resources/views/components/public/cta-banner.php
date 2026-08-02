<?php
declare(strict_types=1);
$cta=$cta??null;if(!$cta)return;
$image=trim((string)($cta['image_path']??''));
$background=preg_match('/^#[0-9A-Fa-f]{6}$/',(string)($cta['background_color']??''))?$cta['background_color']:'#0B182E';
$spray=[[8,18,4,.55],[15,72,2,.4],[28,12,3,.7],[38,82,4,.35],[47,22,2,.6],[58,68,3,.45],[66,15,2,.7],[73,88,4,.4],[84,28,3,.6],[92,73,2,.5],[52,45,2,.35],[33,55,3,.5]];
?>
<div class="py-8 sm:py-10 lg:py-12">
<section class="relative isolate overflow-hidden rounded-2xl text-white" style="background-color:<?=htmlspecialchars($background)?>">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden opacity-70">
        <div class="absolute -left-24 top-1/2 h-72 w-72 -translate-y-1/2 rounded-full bg-blue-400/15 blur-3xl"></div>
        <?php foreach($spray as [$left,$top,$size,$opacity]):?><span class="absolute rounded-full bg-blue-300 shadow-[0_0_12px_rgba(96,165,250,.75)]" style="left:<?=$left?>%;top:<?=$top?>%;width:<?=$size?>px;height:<?=$size?>px;opacity:<?=$opacity?>"></span><?php endforeach;?>
    </div>
    <div class="relative mx-auto grid max-w-[1600px] items-stretch md:grid-cols-2">
        <div class="flex items-center px-6 py-12 sm:px-10 sm:py-16 lg:px-14 lg:py-20">
            <div class="max-w-xl">
                <?php if(trim((string)($cta['badge']??''))!==''){$badgeLabel=(string)$cta['badge'];$badgeVariant='solid';$badgeColor='danger';$badgeSize='md';$badgeIcon='';$badgeRounded='full';$badgeRemovable=false;$badgeOnRemove='';$badgeAttributes=[];$badgeClass='uppercase tracking-[.16em] text-[11px] font-extrabold';require BASE_PATH.'/resources/views/components/base/badge.php';}?>
                <h2 class="mt-4 text-3xl font-black leading-[1.08] tracking-tight sm:text-4xl lg:text-5xl"><?=htmlspecialchars($cta['title'])?></h2>
                <?php if(trim((string)($cta['description']??''))!==''):?><p class="mt-5 text-sm leading-7 text-white/75 sm:text-base"><?=nl2br(htmlspecialchars($cta['description']))?></p><?php endif;?>
                <?php if(trim((string)($cta['button_label']??''))!==''&&trim((string)($cta['link_url']??''))!==''){$buttonLabel=(string)$cta['button_label'];$buttonVariant='solid';$buttonColor='primary';$buttonSize='lg';$buttonType='button';$buttonHref=(string)$cta['link_url'];$buttonName='';$buttonValue='';$buttonId='';$buttonIcon='';$buttonIconTrailing='<span aria-hidden="true">&rarr;</span>';$buttonIconOnly=false;$buttonFullWidth=false;$buttonDisabled=false;$buttonLoading=false;$buttonOnclick='';$buttonClass='mt-7 uppercase tracking-wide';$buttonAttributes=[];require BASE_PATH.'/resources/views/components/base/button.php';}?>
            </div>
        </div>
        <?php if($image!==''):?><div class="min-h-64 overflow-hidden md:min-h-full"><img src="<?=htmlspecialchars($image)?>" alt="<?=htmlspecialchars($cta['title'])?>" class="h-full min-h-64 w-full object-cover" loading="lazy"></div><?php endif;?>
    </div>
</section>
</div>
