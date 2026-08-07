<?php
declare(strict_types=1);

/**
 * Global stepper drawer shell.
 *
 * Required: $stepperDrawerId, $stepperDrawerSteps, $stepperDrawerBody
 * Optional: $stepperDrawerTitle, $stepperDrawerDescription, $stepperDrawerPrefix,
 * $stepperDrawerFormId, $stepperDrawerSubmitLabel, $stepperDrawerTrigger,
 * $stepperDrawerSize, $stepperDrawerExternalNavigation, $stepperDrawerShowHeader.
 */
$stepperDrawerId = (string) ($stepperDrawerId ?? 'stepper-drawer-' . uniqid());
$stepperDrawerSteps = array_values((array) ($stepperDrawerSteps ?? []));
$stepperDrawerBody = (string) ($stepperDrawerBody ?? '');
$stepperDrawerTitle = (string) ($stepperDrawerTitle ?? 'Complete details');
$stepperDrawerDescription = (string) ($stepperDrawerDescription ?? '');
$stepperDrawerPrefix = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) ($stepperDrawerPrefix ?? $stepperDrawerId)) ?: 'stepper';
$stepperDrawerFormId = (string) ($stepperDrawerFormId ?? $stepperDrawerPrefix . '-form');
$stepperDrawerSubmitLabel = (string) ($stepperDrawerSubmitLabel ?? 'Save');
$stepperDrawerTrigger = (string) ($stepperDrawerTrigger ?? '<span class="hidden" aria-hidden="true"></span>');
$stepperDrawerSize = (string) ($stepperDrawerSize ?? 'lg');
$stepperDrawerExternalNavigation = (bool) ($stepperDrawerExternalNavigation ?? false);
$stepperDrawerShowHeader = (bool) ($stepperDrawerShowHeader ?? true);
if ($stepperDrawerSteps === []) throw new InvalidArgumentException('Stepper drawer requires at least one step.');

$drawerHeaderBottom = '';
if ($stepperDrawerShowHeader) {
    ob_start();
    ?>
    <div data-stepper-drawer-header="<?= htmlspecialchars($stepperDrawerPrefix) ?>" class="select-none" aria-label="Step progress">
        <div class="relative h-1 bg-primary/10" role="progressbar" aria-valuemin="1" aria-valuemax="<?= count($stepperDrawerSteps) ?>" aria-valuenow="1" aria-label="Product form progress">
            <div id="<?= htmlspecialchars($stepperDrawerPrefix) ?>-step-bar" class="absolute inset-y-0 left-0 bg-primary transition-all duration-300 ease-out" style="width:20%"></div>
        </div>
    </div>
    <?php
    $drawerHeaderBottom = (string) ob_get_clean();
}

$drawerBody = $stepperDrawerBody;
$drawerFooter = '<button id="' . htmlspecialchars($stepperDrawerPrefix) . '-btn-back" type="button" class="hidden rounded-xl border border-primary/20 px-5 py-2.5 text-sm font-semibold text-secondary hover:bg-primary/5">Back</button><button id="' . htmlspecialchars($stepperDrawerPrefix) . '-btn-primary" type="button" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white hover:bg-secondary">Save &amp; Continue</button>';
$drawerId = $stepperDrawerId;
$drawerSide = 'right';
$drawerSize = $stepperDrawerSize;
$drawerTitle = $stepperDrawerTitle;
$drawerDescription = $stepperDrawerDescription;
$drawerTrigger = $stepperDrawerTrigger;
$drawerStatic = false;
$drawerCloseOnEsc = true;
$drawerShowCloseButton = true;
$drawerOverlay = true;
$drawerHeaderBottom = $drawerHeaderBottom;
require BASE_PATH . '/resources/views/components/base/drawer.php';
?>
<?php if (!$stepperDrawerExternalNavigation): ?>
<script>
(function(){
    var drawer=document.getElementById(<?= json_encode($stepperDrawerId) ?>), total=<?= count($stepperDrawerSteps) ?>, current=1;
    if(!drawer)return;
    var back=document.getElementById(<?= json_encode($stepperDrawerPrefix . '-btn-back') ?>), primary=document.getElementById(<?= json_encode($stepperDrawerPrefix . '-btn-primary') ?>);
    function valid(){var panel=drawer.querySelector('[data-step-panel="'+current+'"]');if(!panel)return true;var fields=panel.querySelectorAll('input,select,textarea');for(var i=0;i<fields.length;i++){if(!fields[i].checkValidity()){fields[i].reportValidity();return false;}}return true;}
    function show(step){current=Math.max(1,Math.min(total,step));drawer.querySelectorAll('[data-step-panel]').forEach(function(panel){panel.classList.toggle('hidden',Number(panel.dataset.stepPanel)!==current);});back.classList.toggle('hidden',current===1);primary.textContent=current===total?<?= json_encode($stepperDrawerSubmitLabel) ?>:'Save & Continue';primary.type=current===total?'submit':'button';if(current===total)primary.setAttribute('form',<?= json_encode($stepperDrawerFormId) ?>);else primary.removeAttribute('form');}
    drawer.addEventListener('click',function(event){var step=event.target.closest('[data-step-btn]');if(step){var target=Number(step.dataset.stepBtn);if(target<=current||valid())show(target);}if(event.target.closest('#'+CSS.escape(<?= json_encode($stepperDrawerPrefix . '-btn-back') ?>)))show(current-1);if(event.target.closest('#'+CSS.escape(<?= json_encode($stepperDrawerPrefix . '-btn-primary') ?>))&&current<total&&valid())show(current+1);});show(1);
})();
</script>
<?php endif; ?>
