<?php
declare(strict_types=1);

namespace StephanSchuler\JsonApi\Queue\Arguments\IncludeRelationships;

class Whitelist implements IncludeRelationships
{
    private $whitelist = [];

    public function __construct(string ... $propertyPaths)
    {
        foreach ($propertyPaths as $propertyPath) {
            $propertyPath = array_filter(explode('.', $propertyPath));
            while ($propertyPath) {
                $this->whitelist[] = join('.', $propertyPath);
                array_pop($propertyPath);
            }
        }
        $this->whitelist = array_unique($this->whitelist);
    }

    public function shouldPropertyPathBeIncluded(string $propertyPath): bool
    {
        return in_array($propertyPath, $this->whitelist);
    }
}