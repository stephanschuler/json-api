<?php

namespace StephanSchuler\JsonApi\Demo\Service;

use StephanSchuler\JsonApi\PropertyAccess\Properties;
use StephanSchuler\JsonApi\PropertyAccess\Property;
use StephanSchuler\JsonApi\Resolver;
use StephanSchuler\JsonApi\Resource\Serializer;

class ResourceSerializer implements Serializer
{
    const ATTRIBUTES_PREFIX = 'attribute';

    const SINGLE_RELATION_PREFIX = 'single';

    const COLLECTION_RELATION_PREFIX = 'collection';

    private $resolver;

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function canSerialize(string $typeName): bool
    {
        return true;
    }

    public function hasAttributes(string $typeName): bool
    {
        return count($this->getAttributeAccessors($typeName));
    }

    public function hasRelations(string $typeName): bool
    {
        return count($this->getCollectionRelationAccessors($typeName)) || count($this->getSingleRelationAccessors($typeName));
    }

    public function getAttributeAccessors(string $typeName): Properties
    {
        return $this->getClassMethodsForType($typeName, self::ATTRIBUTES_PREFIX);
    }

    public function getSingleRelationAccessors(string $typeName): Properties
    {
        return $this->getClassMethodsForType($typeName, self::SINGLE_RELATION_PREFIX);
    }

    public function getCollectionRelationAccessors(string $typeName): Properties
    {
        return $this->getClassMethodsForType($typeName, self::COLLECTION_RELATION_PREFIX);
    }

    protected function getClassMethodsForType(string $typeName, string $prefix): Properties
    {
        $className = $this->resolver
            ->getIdentifierForTypeName($typeName)
            ->getClassNameForTypeName($typeName);
        return array_reduce(
            get_class_methods($className),
            function (Properties $properties, string $methodName) use ($prefix) {
                if (substr($methodName, 0, strlen($prefix)) !== $prefix) {
                    return $properties;
                }
                $propertyName = lcfirst(substr($methodName, strlen($prefix)));
                if (strlen($propertyName) === 0) {
                    return $properties;
                }
                return $properties->withProperty(new Property(
                    $propertyName,
                    function ($subject) use ($methodName) {
                        return ([$subject, $methodName])();
                    }
                ));
            },
            new Properties()
        );
    }
}