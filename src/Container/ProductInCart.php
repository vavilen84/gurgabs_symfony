<?php

namespace App\Container;

use App\Entity\Product;

class ProductInCart
{
    /** @var App\Entity\Product */
    private $product;

    /** @var int */
    private $quantity;

    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity ?? 0;
    }
}
