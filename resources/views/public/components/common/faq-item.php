<?php
/**
 * Variables:
 * @var string $question
 * @var string $answer
 * @var string $id Unique element ID
 */
?>
<div class="border-b border-slate-200 py-4" data-accordion>
    <dt>
        <button type="button" class="w-full flex justify-between items-start text-left text-slate-800 font-bold focus:outline-none focus:text-primary" aria-controls="faq-ans-<?= e($id) ?>" aria-expanded="false" data-accordion-trigger>
            <span class="text-base"><?= e($question) ?></span>
            <span class="ml-6 h-7 flex items-center text-slate-400" data-accordion-icon>
                <?php component('public/components/common/icon', ['name' => 'chevron-down', 'size' => 'sm', 'class' => 'transform transition-transform']); ?>
            </span>
        </button>
    </dt>
    <dd class="mt-2 pr-12 hidden" id="faq-ans-<?= e($id) ?>">
        <p class="text-sm text-slate-500 leading-relaxed"><?= e($answer) ?></p>
    </dd>
</div>