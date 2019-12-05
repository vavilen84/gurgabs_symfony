<?php

namespace App\TwigExtensions;

use App\Container\ProductInCart;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CartQuantity extends AbstractExtension
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction("cart_quantity", array($this, "getCartQuantity")),
        );
    }

    public function getFilters()
    {
        return [];
    }

    public function getCartQuantity(): int
    {
        $cart = $this->session->get('cart');
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $id => $container) {
                if ($container instanceof ProductInCart) {
                    $total += $container->getQuantity();
                }
            }
        }
        return $total;
    }
}
