<?php

namespace StephanSchuler\JsonApi\PropertyAccess;

class Property
{
    private $name;

    private $accessor;

    public function __construct(string $name, callable $accessor)
    {
        $this->name = $name;
        $this->accessor = $accessor;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function __invoke($subject)
    {
        return ($this->accessor)($subject);
    }
}