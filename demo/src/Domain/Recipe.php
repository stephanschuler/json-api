<?php

namespace StephanSchuler\JsonApi\Demo\Domain;

use StephanSchuler\JsonApi\Demo\Domain\Ingredient\Ingredient;
use StephanSchuler\JsonApi\Demo\Domain\Ingredient\Relation;
use StephanSchuler\JsonApi\Demo\Domain\Quantity\Quantity;
use StephanSchuler\JsonApi\Demo\Domain\Step\Step;

class Recipe
{
    private $name;

    private $source;

    private $ingredients = [];

    private $steps = [];

    protected function __construct(string $name, string $source)
    {
        $this->name = $name;
        $this->source = $source;
    }

    public function attributeName(): string
    {
        return $this->name;
    }

    public function attributeSource(): string
    {
        return $this->source;
    }

    public function collectionIngredients(): array
    {
        return $this->ingredients;
    }

    public function collectionSteps(): array
    {
        return $this->steps;
    }

    public static function create(string $name): self
    {
        return new self($name, '');
    }

    public static function createFromSource(string $name, string $source): self
    {
        return new self($name, $source);
    }

    public function withIngredient(Ingredient $ingredient, Quantity $quantity): self
    {
        $clone = clone $this;
        $clone->ingredients[] = Relation::create($ingredient, $quantity);
        return $clone;
    }

    public function withStep(string $description, Ingredient ... $ingredients)
    {
        $clone = clone $this;
        $clone->steps[] = Step::create($description, ... $ingredients);
        return $clone;
    }

    public function withSource(string $source)
    {
        $clone = clone $this;
        $clone->source = $source;
        return $clone;
    }
}