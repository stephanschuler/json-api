<?php

namespace StephanSchuler\JsonApi\Schema;

class Identity
{
    private $id;

    private $type;

    public function __construct(string $typeName, string $id = null)
    {
        $this->id = $id;
        $this->type = $typeName;
    }

    public function hasId(): bool
    {
        return is_string($this->id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTypeName(): string
    {
        return $this->type;
    }

    public function toString(): string
    {
        return $this->getTypeName() . '#' . $this->getId();
    }
}