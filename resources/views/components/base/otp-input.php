<?php

declare(strict_types=1);

/**
 * OTP Input component — single digit input boxes for one-time passwords.
 *
 * Usage (in any view/component):
 *   <?php require BASE_PATH . '/resources/views/components/base/otp-input.php'; ?>
 *
 * -----------------------------------------------------------------------------
 * Available options (set BEFORE requiring this file):
 * -----------------------------------------------------------------------------
 *   $otpInputName        string   name attribute for the combined hidden input.
 *   $otpInputId          string   id for the hidden input (defaults to $otpInputName).
 *   $otpLength           int      Number of single digit boxes (default: 6).
 *   $otpInputValue       string   Pre-filled OTP code (default: '').
 *   $otpInputLabel       string   Label shown above the field.
 *   $otpInputHint        string   Small helper text under the field.
 *   $otpInputError       string   Error message (switches state to "error").
 *   $otpInputSize        string   sm | md | lg                                (default: md)
 *   $otpInputState       string   default | error | success | disabled
 *   $otpInputRequired    bool     Add a * to the label.
 *   $otpInputDisabled    bool     Disable the input boxes.
 *   $otpInputClass       string   Extra CSS classes on the container.
 *
 * @var string|null  $otpInputName
 * @var string|null  $otpInputId
 * @var int          $otpLength
 * @var string|null  $otpInputValue
 * @var string|null  $otpInputLabel
 * @var string|null  $otpInputHint
 * @var string|null  $otpInputError
 * @var string       $otpInputSize
 * @var string       $otpInputState
 * @var bool         $otpInputRequired
 * @var bool         $otpInputDisabled
 * @var string|null  $otpInputClass
 */

$otpInputName     = $otpInputName     ?? 'otp';
$otpInputId       = $otpInputId       ?? $otpInputName;
$otpLength        = (int) ($otpLength ?? 6);
$otpInputValue    = (string) ($otpInputValue ?? '');

$otpInputRequired = $otpInputRequired ?? false;
$otpInputDisabled = $otpInputDisabled ?? false;
$otpInputError    = $otpInputError    ?? '';
$otpInputHint     = $otpInputHint     ?? '';
$otpInputSize     = $otpInputSize     ?? 'md';
$otpInputClass    = $otpInputClass    ?? '';

$otpInputState = $otpInputState ?? ($otpInputError !== '' ? 'error' : 'default');

$otpBoxSizes = [
    'sm' => 'h-10 w-10 text-base',
    'md' => 'h-12 w-12 text-lg',
    'lg' => 'h-14 w-14 text-xl',
];

$otpBoxStates = [
    'default'  => 'border-slate-300 bg-white text-secondary placeholder:text-slate-400 focus:border-primary focus:ring-primary/20',
    'error'    => 'border-rose-500 bg-rose-50/30 text-secondary placeholder:text-slate-400 focus:border-rose-500 focus:ring-rose-500/20',
    'success'  => 'border-emerald-400 bg-emerald-50/30 text-secondary placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500/20',
    'disabled' => 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-500 placeholder:text-slate-400',
];

$otpBoxBase = 'otp-box rounded-lg border text-center font-bold outline-none transition focus:ring-2 shadow-sm';
$otpBoxClasses = trim(implode(' ', [
    $otpBoxBase,
    $otpBoxSizes[$otpInputSize] ?? $otpBoxSizes['md'],
    $otpBoxStates[$otpInputState] ?? $otpBoxStates['default'],
]));

$otpWrapperId = 'otp-wrapper-' . uniqid();
?>
<div class="space-y-1.5 <?= htmlspecialchars($otpInputClass) ?>" id="<?= htmlspecialchars($otpWrapperId) ?>">
    <?php if ($otpInputLabel !== ''): ?>
        <label class="block text-sm font-medium text-secondary">
            <?= htmlspecialchars((string) $otpInputLabel) ?>
            <?php if ($otpInputRequired): ?><span class="text-rose-500">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2.5 items-center" data-otp-group>
        <?php for ($i = 0; $i < $otpLength; $i++): ?>
            <?php
            $char = '';
            if (isset($otpInputValue[$i])) {
                $char = $otpInputValue[$i];
            }
            ?>
            <input
                type="text"
                maxlength="1"
                pattern="[0-9]*"
                inputmode="numeric"
                value="<?= htmlspecialchars($char) ?>"
                class="<?= $otpBoxClasses ?>"
                <?= $otpInputDisabled || $otpInputState === 'disabled' ? ' disabled' : '' ?>
                data-index="<?= $i ?>"
                aria-label="OTP digit <?= $i + 1 ?>"
            >
        <?php endfor; ?>

        <!-- Combined output value -->
        <input
            type="hidden"
            id="<?= htmlspecialchars($otpInputId) ?>"
            name="<?= htmlspecialchars($otpInputName) ?>"
            value="<?= htmlspecialchars($otpInputValue) ?>"
            <?= $otpInputRequired ? 'required' : '' ?>
        >
    </div>

    <?php if ($otpInputError !== ''): ?>
        <p class="text-xs font-medium text-rose-600"><?= htmlspecialchars($otpInputError) ?></p>
    <?php elseif ($otpInputHint !== ''): ?>
        <p class="text-xs text-slate-500"><?= htmlspecialchars($otpInputHint) ?></p>
    <?php endif; ?>
</div>

<script>
(function () {
    const wrapper = document.getElementById('<?= $otpWrapperId ?>');
    if (!wrapper) return;

    const inputs = wrapper.querySelectorAll('input[data-index]');
    const hiddenInput = wrapper.querySelector('input[type="hidden"]');
    const otpLength = <?= $otpLength ?>;

    function updateValue() {
        let value = '';
        inputs.forEach(input => {
            value += input.value;
        });
        hiddenInput.value = value;
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            input.value = input.value.replace(/[^0-9]/g, '');
            updateValue();

            if (input.value !== '') {
                const next = inputs[index + 1];
                if (next) {
                    next.focus();
                    next.select();
                }
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace') {
                if (input.value === '') {
                    const prev = inputs[index - 1];
                    if (prev) {
                        prev.focus();
                        prev.value = '';
                        updateValue();
                    }
                } else {
                    input.value = '';
                    updateValue();
                }
                e.preventDefault();
            } else if (e.key === 'ArrowLeft') {
                const prev = inputs[index - 1];
                if (prev) {
                    prev.focus();
                }
            } else if (e.key === 'ArrowRight') {
                const next = inputs[index + 1];
                if (next) {
                    next.focus();
                }
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '');
            if (!pasteData) return;

            let fillIndex = 0;
            for (let i = 0; i < otpLength && fillIndex < pasteData.length; i++) {
                if (inputs[i]) {
                    inputs[i].value = pasteData[fillIndex++];
                }
            }
            updateValue();

            const focusTarget = inputs[Math.min(pasteData.length, otpLength - 1)];
            if (focusTarget) {
                focusTarget.focus();
            }
        });

        input.addEventListener('focus', () => {
            input.select();
        });
    });
})();
</script>
