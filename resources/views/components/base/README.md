# GiftVibe Base UI Components

Reusable UI building blocks for the GiftVibe MVC app. Every component is a plain PHP
view file that you include with:

```php
<?php require BASE_PATH . '/resources/views/components/base/button.php'; ?>
```

Set the variables you need **before** requiring the file — anything you leave out
falls back to a sensible default. Each component file has a docblock at the top with
the full option reference and examples.

Interactive components (dropdown, datatable, modal, drawer) ship their own tiny
vanilla-JS bootstrap. The script is guarded by a global `window.GiftVibeUI` registry,
so including a component many times on one page never duplicates event listeners.

---

## Available components

| Component | File | Interactive |
|-----------|------|-------------|
| Button | `button.php` | – |
| Input | `input.php` | – |
| Select | `select.php` | – |
| Date input | `date-input.php` | – |
| Phone input | `phone-input.php` | – |
| Dropdown menu | `dropdown-menu.php` | ✅ |
| Data table | `datatable.php` | ✅ |
| Modal | `modal.php` | ✅ |
| Drawer | `drawer.php` | ✅ |
| Badge | `badge.php` | – |

There is also a **live preview page** at `/base` (route registered in `routes/web.php`,
controller `app/Controllers/Public/BaseController.php`) that renders every component
and variant, so you can see the library before wiring it into a real screen.

---

## Button

```php
$buttonLabel   = 'Save';
$buttonVariant = 'solid';   // solid | outline | soft | ghost | link
$buttonColor   = 'primary'; // primary | secondary | accent | success | warning | danger
$buttonSize    = 'md';      // xs | sm | md | lg | xl
require 'button.php';
```

Other options: `$buttonHref`, `$buttonIcon`, `$buttonIconTrailing`, `$buttonIconOnly`,
`$buttonFullWidth`, `$buttonLoading`, `$buttonDisabled`, `$buttonOnclick`,
`$buttonClass`, `$buttonAttributes`, `$buttonName`, `$buttonValue`, `$buttonId`.

```php
// Outline + icon, small
$buttonLabel = 'Add';
$buttonVariant = 'outline';
$buttonColor = 'success';
$buttonSize = 'sm';
$buttonIcon = '<svg class="h-4 w-4" ...>…</svg>';
require 'button.php';

// Icon-only round button
$buttonIcon = '<svg class="h-4 w-4">…</svg>';
$buttonIconOnly = true;
$buttonVariant = 'soft';
$buttonAttributes = ['title' => 'Edit'];
require 'button.php';
```

---

## Input

```php
$inputName        = 'email';
$inputType        = 'email';       // text | email | password | number | search | url | tel
$inputLabel       = 'Email';
$inputPlaceholder = 'you@example.com';
$inputRequired    = true;
require 'input.php';
```

Other options: `$inputValue`, `$inputHint`, `$inputError` (switches to error state),
`$inputSize` (`sm|md|lg`), `$inputState` (`default|error|success|disabled`),
`$inputLeadingIcon` / `$inputTrailingIcon` (inline SVG), `$inputPrefix` / `$inputSuffix`
(text), `$inputAutocomplete`, `$inputReadonly`, `$inputDisabled`, `$inputAttributes`,
`$inputClass`.

---

## Select

```php
$selectName    = 'category';
$selectLabel   = 'Category';
$selectOptions = [
    '' => 'Choose…',
    'gifts'   => 'Gifts',
    'flowers' => 'Flowers',
];
$selectValue = 'gifts';
require 'select.php';
```

Supports full option specs, optgroups and multi-select:

```php
$selectName = 'tags[]';
$selectMultiple = true;
$selectOptions = [
    ['label' => 'Occasions', 'options' => [
        'birthday' => 'Birthday',
        'wedding'  => 'Wedding',
    ]],
    ['value' => 'custom', 'label' => 'Custom', 'disabled' => true],
];
$selectValue = ['birthday'];
require 'select.php';
```

Other options: `$selectPlaceholder`, `$selectHint`, `$selectError`, `$selectSize`,
`$selectState`, `$selectRequired`, `$selectDisabled`, `$selectAttributes`,
`$selectClass`.

---

## Date input

```php
$dateInputName  = 'event_date';
$dateInputLabel = 'Event date';
$dateInputMin   = date('Y-m-d');   // optional
$dateInputMax   = '2026-12-31';    // optional
require 'date-input.php';
```

Other options: `$dateInputValue` (Y-m-d), `$dateInputHint`, `$dateInputError`,
`$dateInputSize`, `$dateInputState`, `$dateInputRequired`, `$dateInputDisabled`,
`$dateInputAttributes`, `$dateInputClass`.

---

## Phone input

```php
$phoneInputName  = 'mobile';
$phoneInputLabel = 'Mobile number';
$phoneCodeValue  = '94';           // default: 94 (Sri Lanka)
require 'phone-input.php';
```

The country-code dropdown is a separate field (`$phoneCodeName`, default `country_code`).
Pass a custom `$phoneCountries` list if you need more countries:

```php
$phoneCountries = [
    ['code' => 'US', 'dial' => '1',  'flag' => '🇺🇸', 'name' => 'United States'],
    ['code' => 'LK', 'dial' => '94', 'flag' => '🇱🇰', 'name' => 'Sri Lanka'],
];
require 'phone-input.php';
```

---

## Dropdown menu

```php
$dropdownId    = 'actions';
$dropdownLabel = 'Actions';
$dropdownItems = [
    ['label' => 'Edit',   'href' => '/products/1/edit', 'icon' => '<svg class="h-4 w-4">…</svg>'],
    ['type'  => 'divider'],
    ['label' => 'Delete', 'danger' => true, 'onclick' => "confirm('Delete?')"],
];
require 'dropdown-menu.php';
```

Menu entries can be: plain strings, items (`label`, `href`, `icon`, `onclick`,
`disabled`, `active`, `danger`, `keepOpen`), `['type' => 'divider']` and
`['type' => 'header', 'label' => '…']`.

Trigger styling: `$dropdownTriggerVariant`, `$dropdownTriggerColor`,
`$dropdownTriggerSize`, `$dropdownTriggerIcon`, or pass raw `$dropdownTrigger` HTML.
Menu position: `$dropdownAlign` (`left|right`), width `$dropdownWidth` (`w-48|w-56|w-64`).

---

## Data table

```php
$tableId = 'products';
$tableColumns = [
    ['key' => 'name',  'label' => 'Product'],
    ['key' => 'price', 'label' => 'Price', 'type' => 'number', 'align' => 'right',
     'format' => fn ($v) => 'LKR ' . number_format((float) $v, 2)],
    ['label' => '', 'align' => 'right', 'width' => 'w-28'],   // actions column
];
$tableRows = [
    ['name' => 'Teddy Bear', 'price' => 1500, '_actions' => '<a href="#">Edit</a>'],
];
require 'datatable.php';
```

Built-in behaviour (client-side, no dependencies):

- 🔍 Search box (`$tableSearchable`, `$tableSearchPlaceholder`)
- ↕️ Click a column header to sort (`$tableSortable`, per-column `type: string|number|date`,
  `$tableDefaultSort = ['key' => 'name', 'dir' => 'asc']`)
- 📄 Pagination (`$tablePaginated`, `$tablePerPage`)
- 🦓 Zebra striping (`$tableZebra`), empty state (`$tableEmptyMessage`),
  totals footer (`$tableFooter`)

Columns support `format` (escaped by default), `html => true`, and `render`
(full custom cell, not escaped). Extra attributes on the `<table>` go in
`$tableAttributes`.

---

## Modal

```php
$modalId = 'delete-product';
$modalTriggerLabel = 'Delete';
$modalTriggerVariant = 'outline';
$modalTriggerColor = 'danger';
$modalTitle = 'Delete product?';
$modalBody = '<p>This action cannot be undone.</p>';
$modalFooter = '
    <button data-modal-close class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium">Cancel</button>
    <button data-modal-close class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white">Delete</button>
';
require 'modal.php';
```

Options: `$modalDescription`, `$modalSize` (`sm|md|lg|xl|full`), `$modalStatic`,
`$modalCloseOnEsc`, `$modalScrollable`, `$modalShowCloseButton`, `$modalTrigger`
(raw HTML), `$modalTriggerIcon`.

Any element with `data-modal-close` inside the modal closes it. Closes on Escape and
backdrop click (unless `$modalStatic`).

---

## Drawer

```php
$drawerId = 'cart';
$drawerSide = 'right';          // left | right
$drawerTriggerLabel = 'View cart';
$drawerTitle = 'Your cart';
$drawerBody = '<p>Cart contents…</p>';
$drawerFooter = '<button data-drawer-close class="…">Continue shopping</button>';
require 'drawer.php';
```

Options: `$drawerSize` (`sm|md|lg|xl`), `$drawerStatic`, `$drawerCloseOnEsc`,
`$drawerOverlay`, `$drawerShowCloseButton`, `$drawerTrigger` (raw HTML),
`$drawerTriggerIcon`, `$drawerTriggerVariant`, `$drawerTriggerColor`,
`$drawerTriggerSize`.

Any element with `data-drawer-close` inside the drawer closes it. Slides in/out with
a CSS transform transition.

---

## Badge

```php
$badgeLabel   = 'In stock';
$badgeColor   = 'success'; // primary | secondary | accent | success | warning | danger | neutral
$badgeVariant = 'soft';    // solid | soft | outline | dot
require 'badge.php';
```

Other options: `$badgeSize` (`sm|md|lg`), `$badgeRounded` (`full|md|lg`),
`$badgeIcon`, `$badgeRemovable`, `$badgeOnRemove`, `$badgeAttributes`, `$badgeClass`.

---

## Theme

All components use the project theme defined in `public/assets/css/global.css`
(Tailwind v4 `@theme`): `primary` (indigo), `secondary` (slate-900), `accent` (pink),
plus standard Tailwind emerald / amber / rose for status colours.
