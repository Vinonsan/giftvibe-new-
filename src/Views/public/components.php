<?php
use Components\Base\Alert;
use Components\Base\Badge;
use Components\Base\Button;
use Components\Base\Card;
use Components\Base\Input;
use Components\Drawer\Drawer;
use Components\Modal\Modal;

$variants = ['primary', 'secondary', 'dark', 'light', 'danger', 'info', 'success'];
?>

<div class="mx-auto max-w-6xl space-y-8">
    <section>
        <p class="text-sm font-semibold uppercase tracking-widest text-primary">Design system</p>
        <h1 class="mt-2 text-3xl font-black text-dark">Base Component Showcase</h1>
        <p class="mt-2 text-secondary">Theme tokens and reusable PHP components in one place.</p>
    </section>

    <?= Card::render('Theme colours', '
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-8">
            <div><div class="h-16 rounded-xl bg-primary"></div><p class="mt-2 text-xs font-semibold">primary</p></div>
            <div><div class="h-16 rounded-xl bg-secondary"></div><p class="mt-2 text-xs font-semibold">secondary</p></div>
            <div><div class="h-16 rounded-xl bg-dark"></div><p class="mt-2 text-xs font-semibold">dark</p></div>
            <div><div class="h-16 rounded-xl border bg-light"></div><p class="mt-2 text-xs font-semibold">light</p></div>
            <div><div class="h-16 rounded-xl bg-danger"></div><p class="mt-2 text-xs font-semibold">danger</p></div>
            <div><div class="h-16 rounded-xl bg-info"></div><p class="mt-2 text-xs font-semibold">info</p></div>
            <div><div class="h-16 rounded-xl bg-success"></div><p class="mt-2 text-xs font-semibold">success</p></div>
            <div><div class="h-16 rounded-xl bg-warning"></div><p class="mt-2 text-xs font-semibold">warning</p></div>
        </div>
    ') ?>

    <?= Card::render('Buttons', '<div class="flex flex-wrap gap-3">' . implode('', array_map(
        fn ($variant) => Button::render(ucfirst($variant), $variant),
        $variants
    )) . Button::render('Ghost', 'ghost') . Button::render('Disabled', 'primary', ['disabled' => true]) . '</div>') ?>

    <?= Card::render('Badges', '<div class="flex flex-wrap gap-3">' . implode('', array_map(
        fn ($variant) => Badge::render(ucfirst($variant), $variant),
        array_merge($variants, ['warning'])
    )) . '</div>') ?>

    <section class="grid gap-6 lg:grid-cols-2">
        <?= Card::render('Alerts',
            Alert::render('Your changes have been saved.', 'success') .
            Alert::render('Please check the highlighted fields.', 'danger') .
            Alert::render('A new update is available.', 'info') .
            Alert::render('This action needs your attention.', 'warning')
        ) ?>

        <?= Card::render('Form inputs', '<div class="space-y-4">' .
            Input::render('full_name', ['label' => 'Full name', 'placeholder' => 'Jane Doe', 'required' => true]) .
            Input::render('email', ['label' => 'Email address', 'type' => 'email', 'placeholder' => 'jane@example.com', 'help' => 'We will never share your email.']) .
            Input::render('invalid_email', ['label' => 'Input with error', 'type' => 'email', 'value' => 'invalid-email', 'error' => 'Enter a valid email address.']) .
            '</div>', ['footer' => '<span class="text-xs text-secondary">Label, help, error and disabled states are supported.</span>']) ?>
    </section>

    <?= Card::render('Overlay components', '
        <div class="flex flex-wrap gap-3">
            <button type="button" @click="modal = \'showcase-modal\'" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">Open modal</button>
            <button type="button" @click="drawer = \'showcase-drawer\'" class="rounded-lg bg-dark px-4 py-2 text-sm font-semibold text-white hover:bg-dark-800">Open drawer</button>
        </div>
    ') ?>
</div>

<?= Modal::render('showcase-modal', 'Example modal', '<p class="text-sm text-secondary">This modal is controlled by Alpine.js and uses the shared theme.</p>') ?>
<?= Drawer::render('showcase-drawer', 'Example drawer', '<p class="text-sm text-secondary">Drawer content can contain forms, navigation or supporting details.</p>') ?>
