<?php
declare(strict_types=1);

namespace StephanSchuler\JsonApi\PropertyAccess;

use Countable;
use IteratorAggregate;

class Properties implements IteratorAggregate, Countable
{
    private $properties = [];

    public function __construct(Property ... $properties)
    {
        foreach ($properties as $property) {
            $this->properties[$property->__toString()] = $property;
        }
    }

    public function withProperty(Property $property)
    {
        $result = clone $this;
        $result->properties[$property->__toString()] = $property;
        return $result;
    }

    public function getIterator()
    {
        yield from $this->properties;
    }

    public function count()
    {
        return count($this->properties);
    }
}