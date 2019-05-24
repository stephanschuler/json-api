<?php

namespace StephanSchuler\JsonApi\Demo\Domain\Quantity;


class Uncountable implements Quantity
{
    protected function __construct()
    {
    }

    public static function createQuantity(): self
    {
        return new self();
    }
}