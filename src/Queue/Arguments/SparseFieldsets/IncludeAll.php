<?php
declare(strict_types=1);

namespace StephanSchuler\JsonApi\Queue\Arguments\SparseFieldsets;

class IncludeAll implements SparseFieldsets
{
    public function shouldPropertyBeIncluded(string $typeName, string $propertyName): bool
    {
        return true;
    }

}