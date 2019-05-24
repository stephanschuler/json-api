<?php

namespace StephanSchuler\JsonApi\Resource;

use StephanSchuler\JsonApi\PropertyAccess\Properties;

interface Serializer extends Handler
{
    public function canSerialize(string $typeName): bool;

    public function hasAttributes(string $typeName): bool;

    public function hasRelations(string $typeName): bool;

    public function getAttributeAccessors(string $typeName): Properties;

    public function getSingleRelationAccessors(string $typeName): Properties;

    public function getCollectionRelationAccessors(string $typeName): Properties;
}