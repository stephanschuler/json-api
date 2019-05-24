<?php

namespace StephanSchuler\JsonApi\Demo\Domain\Step;


use StephanSchuler\JsonApi\Demo\Domain\Ingredient\Ingredient;

class Step
{
    private $descirption;

    private $ingredients;

    protected function __construct(string $descirption, Ingredient ... $ingredients)
    {
        $this->descirption = $descirption;
        $this->ingredients = $ingredients;
    }

    public static function create(string $descirption, Ingredient ... $ingredients): self
    {
        return new self($descirption, ... $ingredients);
    }

    public function attributeDescirption(): string
    {
        return $this->descirption;
    }

    public function collectionIngredients(): array
    {
        return $this->ingredients;
    }
}