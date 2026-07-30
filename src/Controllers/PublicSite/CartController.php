<?php

namespace Controllers\PublicSite;

use App\Core\View;
use RuntimeException;
use Services\CartService;

class CartController
{
    public function index(?string $error = null): void
    {
        $cart = new CartService();
        View::renderPage('public/pages/cart', [
            'title' => 'Shopping Cart | GiftVibe.lk',
            'navigation' => $this->navigation('shop'),
            'items' => $cart->items(),
            'totals' => $cart->totals(),
            'error' => $error,
            'meta' => ['description' => 'Review your GiftVibe.lk cart before checkout.', 'url' => url('/cart')],
        ]);
    }

    public function add(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->index('Your session expired. Try again.');
            return;
        }

        try {
            (new CartService())->add(
                (int) ($_POST['product_id'] ?? 0),
                ($_POST['variant_id'] ?? '') !== '' ? (int) $_POST['variant_id'] : null,
                (int) ($_POST['quantity'] ?? 1),
                trim((string) ($_POST['gift_message'] ?? ''))
            );
            redirect('/cart');
        } catch (RuntimeException $exception) {
            $this->index($exception->getMessage());
        }
    }

    public function update(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->index('Your session expired. Try again.');
            return;
        }

        try {
            (new CartService())->update((int) ($_POST['item_id'] ?? 0), (int) ($_POST['quantity'] ?? 1));
            redirect('/cart');
        } catch (RuntimeException $exception) {
            $this->index($exception->getMessage());
        }
    }

    public function remove(): void
    {
        if (!verify_csrf($_POST['_token'] ?? null)) {
            $this->index('Your session expired. Try again.');
            return;
        }

        try {
            (new CartService())->remove((int) ($_POST['item_id'] ?? 0));
            redirect('/cart');
        } catch (RuntimeException $exception) {
            $this->index($exception->getMessage());
        }
    }

    private function navigation(string $active = 'shop'): array
    {
        return [
            ['label' => 'Home', 'url' => url('/'), 'active' => $active === 'home'],
            ['label' => 'Shop', 'url' => url('/shop'), 'active' => $active === 'shop'],
            ['label' => 'Categories', 'url' => url('/categories'), 'active' => false],
            ['label' => 'Custom Gifts', 'url' => url('/custom-gifts'), 'active' => false],
            ['label' => 'About', 'url' => url('/about'), 'active' => false],
            ['label' => 'Contact', 'url' => url('/contact'), 'active' => false],
            ['label' => 'Cart', 'url' => url('/cart'), 'active' => true],
        ];
    }
}
