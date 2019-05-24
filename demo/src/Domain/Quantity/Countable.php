<?php

namespace StephanSchuler\JsonApi\Demo\Domain\Quantity;


class Countable implements Quantity
{
    private $quantity;

    private $weight;

    protected function __construct(int $quantity, string $weight = null)
    {
        $this->quantity = $quantity;
        $this->weight = $weight;
    }

    public static function createQuantity(int $quantity): self
    {
        return new self($quantity, null);
    }

    public static function createWeight(int $quantity, string $weight): self
    {
        return new self($quantity, $weight);
    }

    public function attributeQuantity(): int
    {
        return $this->quantity;
    }

    public function attributeWeight()
    {
        return $this->weight;
    }
}