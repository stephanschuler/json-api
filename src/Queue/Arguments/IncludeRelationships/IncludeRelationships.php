<?php
declare(strict_types=1);

namespace StephanSchuler\JsonApi\Queue\Arguments\IncludeRelationships;

interface IncludeRelationships
{
    public function shouldPropertyPathBeIncluded(string $propertyPath): bool;
}