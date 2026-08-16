<?php

declare(strict_types=1);

/**
 * Reusable stepper modal.
 *
 * Required:
 *   $stepperModalId    Unique modal id.
 *   $stepperSteps      [['id' => 'details', 'label' => 'Details', 'body' => '<input ...>'], ...]
 *
 * Optional:
 *   $stepperTitle, $stepperDescription, $stepperTrigger, $stepperTriggerLabel
 *   $stepperFormId, $stepperFormAction, $stepperFormMethod, $stepperFormAttributes
 *   $stepperHiddenFields, $stepperSubmitLabel, $stepperSize, $stepperStartStep
 *   $stepperStatic, $stepperCloseOnEsc, $stepperShowCloseButton
 *
 * Each step body may contain normal required form controls. Next validates only
 * the visible step; a step without required controls remains optional.
 *
 * Example:
 *   $stepperModalId = 'customer-wizard';
 *   $stepperTitle = 'Create customer';
 *   $stepperSteps = [
 *       ['id' => 'profile', 'label' => 'Profile', 'body' => '<input required name="name">'],
 *       ['id' => 'address', 'label' => 'Address', 'body' => '<input name="city">'],
 *   ];
 *   require BASE_PATH . '/resources/views/components/modal/stepper-modal.php';
 */

$stepperModalId = (string) ($stepperModalId ?? 'stepper-modal-' . uniqid());
$stepperSteps = array_values(array_filter((array) ($stepperSteps ?? []), static fn ($step): bool => is_array($step)));
$stepperTitle = (string) ($stepperTitle ?? 'Complete details');
$stepperDescription = (string) ($stepperDescription ?? 'Follow the steps to continue.');
$stepperTrigger = (string) ($stepperTrigger ?? '');
$stepperTriggerLabel = (string) ($stepperTriggerLabel ?? 'Open');
$stepperFormId = (string) ($stepperFormId ?? $stepperModalId . '-form');
$stepperFormAction = (string) ($stepperFormAction ?? '');
$stepperFormMethod = strtolower((string) ($stepperFormMethod ?? 'post'));
$stepperFormMethod = in_array($stepperFormMethod, ['get', 'post'], true) ? $stepperFormMethod : 'post';
$stepperFormAttributes = (array) ($stepperFormAttributes ?? []);
$stepperHiddenFields = (array) ($stepperHiddenFields ?? []);
$stepperSubmitLabel = (string) ($stepperSubmitLabel ?? 'Complete');
$stepperSize = (string) ($stepperSize ?? 'xl');
$stepperStartStep = max(1, min(count($stepperSteps), (int) ($stepperStartStep ?? 1)));
$stepperStatic = (bool) ($stepperStatic ?? true);
$stepperCloseOnEsc = (bool) ($stepperCloseOnEsc ?? true);
$stepperShowCloseButton = (bool) ($stepperShowCloseButton ?? true);

if ($stepperSteps === []) {
    throw new InvalidArgumentException('Stepper modal requires at least one step.');
}

$formAttributeHtml = '';
foreach ($stepperFormAttributes as $name => $value) {
    if (!preg_match('/^[a-zA-Z_:][-a-zA-Z0-9_:.]*$/', (string) $name)) continue;
    $formAttributeHtml .= ' ' . htmlspecialchars((string) $name, ENT_QUOTES) . '="' . htmlspecialchars((string) $value, ENT_QUOTES) . '"';
}

ob_start();
?>
<form id="<?= htmlspecialchars($stepperFormId) ?>" action="<?= htmlspecialchars($stepperFormAction) ?>" method="<?= $stepperFormMethod ?>"<?= $formAttributeHtml ?> data-stepper-form="<?= htmlspecialchars($stepperModalId) ?>">
    <?php foreach ($stepperHiddenFields as $name => $value): ?>
        <input type="hidden" name="<?= htmlspecialchars((string) $name) ?>" value="<?= htmlspecialchars((string) $value) ?>">
    <?php endforeach; ?>

    <nav class="mb-6" aria-label="Progress">
        <ol class="flex gap-2">
            <?php foreach ($stepperSteps as $index => $step): $number = $index + 1; ?>
                <li class="min-w-0 flex-1">
                    <button type="button" data-stepper-go="<?= $number ?>" class="group flex w-full flex-col gap-2 text-left" aria-current="<?= $number === $stepperStartStep ? 'step' : 'false' ?>">
                        <span data-stepper-line class="h-1 rounded-full <?= $number <= $stepperStartStep ? 'bg-primary' : 'bg-primary/10' ?>"></span>
                        <span class="flex items-center gap-2 truncate text-xs font-bold <?= $number === $stepperStartStep ? 'text-primary' : 'text-secondary/50' ?>">
                            <span data-stepper-number class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg <?= $number <= $stepperStartStep ? 'bg-primary text-white' : 'bg-primary/10 text-secondary/50' ?>"><?= $number ?></span>
                            <span class="hidden truncate sm:block"><?= htmlspecialchars((string) ($step['label'] ?? 'Step ' . $number)) ?></span>
                        </span>
                    </button>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>

    <?php foreach ($stepperSteps as $index => $step): $number = $index + 1; ?>
        <section data-stepper-panel="<?= $number ?>" data-step-id="<?= htmlspecialchars((string) ($step['id'] ?? $number)) ?>" class="<?= $number === $stepperStartStep ? '' : 'hidden' ?>" aria-hidden="<?= $number === $stepperStartStep ? 'false' : 'true' ?>">
            <?php if (!empty($step['title'])): ?><h4 class="text-base font-bold text-secondary"><?= htmlspecialchars((string) $step['title']) ?></h4><?php endif; ?>
            <?php if (!empty($step['description'])): ?><p class="mt-1 text-sm text-secondary/60"><?= htmlspecialchars((string) $step['description']) ?></p><?php endif; ?>
            <div class="<?= !empty($step['title']) || !empty($step['description']) ? 'mt-5 ' : '' ?>space-y-4"><?= (string) ($step['body'] ?? '') ?></div>
        </section>
    <?php endforeach; ?>
</form>
<?php
$modalBody = (string) ob_get_clean();

ob_start();
?>
<button type="button" data-stepper-previous="<?= htmlspecialchars($stepperModalId) ?>" class="hidden rounded-xl border border-primary/20 px-5 py-2.5 text-sm font-bold text-secondary hover:bg-primary/5">Previous</button>
<button type="button" data-stepper-next="<?= htmlspecialchars($stepperModalId) ?>" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:bg-secondary">Next</button>
<button type="submit" form="<?= htmlspecialchars($stepperFormId) ?>" data-stepper-submit="<?= htmlspecialchars($stepperModalId) ?>" class="hidden rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:bg-secondary"><?= htmlspecialchars($stepperSubmitLabel) ?></button>
<?php
$modalFooter = (string) ob_get_clean();
$modalId = $stepperModalId;
$modalTitle = $stepperTitle;
$modalDescription = $stepperDescription;
$modalSize = $stepperSize;
$modalStatic = $stepperStatic;
$modalCloseOnEsc = $stepperCloseOnEsc;
$modalScrollable = true;
$modalShowCloseButton = $stepperShowCloseButton;
$modalTrigger = $stepperTrigger;
$modalTriggerLabel = $stepperTriggerLabel;
require BASE_PATH . '/resources/views/components/base/modal.php';
?>
<script>
(function () {
    var root = document.getElementById(<?= json_encode($stepperModalId) ?>);
    if (!root || root.dataset.stepperReady === '1') return;
    root.dataset.stepperReady = '1';
    var form = document.getElementById(<?= json_encode($stepperFormId) ?>);
    var panels = Array.prototype.slice.call(root.querySelectorAll('[data-stepper-panel]'));
    var buttons = Array.prototype.slice.call(root.querySelectorAll('[data-stepper-go]'));
    var previous = root.querySelector('[data-stepper-previous]');
    var next = root.querySelector('[data-stepper-next]');
    var submit = root.querySelector('[data-stepper-submit]');
    var current = <?= $stepperStartStep ?>;

    function validate(step) {
        var panel = panels[step - 1];
        var fields = Array.prototype.slice.call(panel.querySelectorAll('input, select, textarea'));
        for (var i = 0; i < fields.length; i++) {
            if (!fields[i].checkValidity()) { fields[i].reportValidity(); return false; }
        }
        return true;
    }
    function show(step, validateCurrent) {
        step = Math.max(1, Math.min(panels.length, step));
        if (validateCurrent && step > current && !validate(current)) return;
        panels.forEach(function (panel, index) {
            var active = index + 1 === step;
            panel.classList.toggle('hidden', !active);
            panel.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        buttons.forEach(function (button, index) {
            var number = index + 1, reached = number <= step, active = number === step;
            button.setAttribute('aria-current', active ? 'step' : 'false');
            button.querySelector('[data-stepper-line]').className = 'h-1 rounded-full ' + (reached ? 'bg-primary' : 'bg-primary/10');
            button.querySelector('[data-stepper-number]').className = 'flex h-6 w-6 shrink-0 items-center justify-center rounded-lg ' + (reached ? 'bg-primary text-white' : 'bg-primary/10 text-secondary/50');
            button.lastElementChild.classList.toggle('text-primary', active);
            button.lastElementChild.classList.toggle('text-secondary/50', !active);
        });
        previous.classList.toggle('hidden', step === 1);
        next.classList.toggle('hidden', step === panels.length);
        submit.classList.toggle('hidden', step !== panels.length);
        current = step;
        root.dispatchEvent(new CustomEvent('stepper:change', {detail: {step: step, id: panels[step - 1].dataset.stepId}}));
    }
    buttons.forEach(function (button) { button.addEventListener('click', function () { var target = Number(button.dataset.stepperGo); show(target, target > current); }); });
    previous.addEventListener('click', function () { show(current - 1, false); });
    next.addEventListener('click', function () { show(current + 1, true); });
    form.addEventListener('submit', function (event) { if (!validate(current)) event.preventDefault(); });
    root.addEventListener('stepper:reset', function () { show(1, false); });
    show(current, false);
})();
</script>
