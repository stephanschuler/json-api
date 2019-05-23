<?php

namespace StephanSchuler\JsonApi\Demo\Service;

use StephanSchuler\JsonApi\Resolver;
use StephanSchuler\JsonApi\Resource\Identifier;
use StephanSchuler\JsonApi\Schema\Identity;

class ResourceIdentifier implements Identifier
{
    private $resolver;

    private $classNameToTypeMap = [];

    private $typeToClassNameMap = [];

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function identify($subject): Identity
    {
        return new Identity(
            $this->getTypeNameForClassName(get_class($subject)),
            crc32(serialize($subject))
        );
    }

    public function getTypeNameForClassName(string $className): string
    {
        return $this->classNameToTypeMap[$className];
    }

    public function resolvesTypeNameForClassName(string $className): bool
    {
        return isset($this->classNameToTypeMap[$className]);
    }

    public function getClassNameForTypeName(string $className): string
    {
        return $this->typeToClassNameMap[$className];
    }

    public function resolvesClassNameForTypeName(string $typeName): bool
    {
        return isset($this->typeToClassNameMap[$typeName]);
    }

    public function registerResourceClass(string $className): self
    {
        $typeName = join(
            '-',
            array_map(
                function ($word) {
                    return lcfirst($word);
                },
                explode(
                    '\\',
                    $className
                )
            )
        );
        $typeName = str_replace('stephanSchuler-jsonApi-demo-domain-', '', $typeName);
        $this->classNameToTypeMap[$className] = $typeName;
        $this->typeToClassNameMap[$typeName] = $className;
        return $this;
    }
}