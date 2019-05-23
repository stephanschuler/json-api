<?php

namespace StephanSchuler\JsonApi\Demo\Domain\Ingredient;

class Ingredient
{
    private $name;

    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function create(string $name): self
    {
        return new self($name);
    }

    public function attributeName(): string
    {
        return $this->name;
    }
}