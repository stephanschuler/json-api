<?php
declare(strict_types=1);

namespace StephanSchuler\JsonApi\Queue\Arguments\IncludeRelationships;

class DropAll implements IncludeRelationships
{
    public function shouldPropertyPathBeIncluded(string $propertyPath): bool
    {
        return false;
    }
}