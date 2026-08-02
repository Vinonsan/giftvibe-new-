<?php
$homepageCta=$homepageCta??null;if(!$homepageCta)return;$background=preg_match('/^#[0-9A-Fa-f]{6}$/',(string)$homepageCta['background_color'])?$homepageCta['background_color']:'#102E50';$link=trim((string)$homepageCta['link_url']);
?>
<section class="overflow-hidden rounded-3xl text-white shadow-xl" style="background-color:<?=htmlspecialchars($background)?>">
    <div class="grid min-h-72 items-center md:grid-cols-2">
        <div class="p-7 sm:p-10 lg:p-12"><?php if(trim((string)$homepageCta['badge'])!==''):?><span class="inline-flex rounded-md bg-rose-600 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[.2em] text-white shadow-lg"><?=htmlspecialchars($homepageCta['badge'])?></span><?php endif;?><h2 class="mt-4 max-w-xl text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl"><?=htmlspecialchars($homepageCta['title'])?></h2><?php if(trim((string)$homepageCta['description'])!==''):?><p class="mt-4 max-w-xl text-sm leading-6 text-white/75 sm:text-base"><?=htmlspecialchars($homepageCta['description'])?></p><?php endif;?><?php if($link!==''&&trim((string)$homepageCta['button_label'])!==''):?><a href="<?=htmlspecialchars($link)?>" class="mt-7 inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-secondary transition hover:bg-primary hover:text-white"><?=htmlspecialchars($homepageCta['button_label'])?> <span>&rarr;</span></a><?php endif;?></div>
        <div class="h-64 md:h-full"><img src="<?=htmlspecialchars($homepageCta['image_path'])?>" alt="<?=htmlspecialchars($homepageCta['title'])?>" class="h-full w-full object-cover"></div>
    </div>
</section>
