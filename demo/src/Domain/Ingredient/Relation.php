<?php

namespace StephanSchuler\JsonApi\Demo\Domain\Ingredient;

use StephanSchuler\JsonApi\Demo\Domain\Quantity\Quantity;

class Relation
{
    private $ingredient;

    private $quantity;

    protected function __construct(Ingredient $ingredient, Quantity $quantity)
    {
        $this->ingredient = $ingredient;
        $this->quantity = $quantity;
    }

    public static function create(Ingredient $ingredient, Quantity $quantity): self
    {
        return new self($ingredient, $quantity);
    }

    public function singleIngredient(): Ingredient
    {
        return $this->ingredient;
    }

    public function singleQuantity(): Quantity
    {
        return $this->quantity;
    }
}