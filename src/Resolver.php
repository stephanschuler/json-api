<?php

namespace StephanSchuler\JsonApi;

use StephanSchuler\JsonApi\Resource\Identifier;
use StephanSchuler\JsonApi\Resource\Serializer;

class Resolver
{
    /**
     * @var Identifier[]
     */
    private $identifiers = [];

    /**
     * @var Serializer[]
     */
    private $serializers = [];

    public function registerIdentifier(Identifier $identifier): self
    {
        $this->identifiers[] = $identifier;
        return $this;
    }

    public function getIdentifierForSubject($subject): Identifier
    {
        return $this->getIdentifierForClassName(get_class($subject));
    }

    public function getIdentifierForClassName(string $className): Identifier
    {
        foreach ($this->identifiers as $identifier) {
            if ($identifier->resolvesTypeNameForClassName($className)) {
                return $identifier;
            }
        }
        throw new \Exception(sprintf('No identifier can handle instanceof %s', $className));
    }

    public function getIdentifierForTypeName(string $typeName): Identifier
    {
        foreach ($this->identifiers as $identifier) {
            if ($identifier->resolvesClassNameForTypeName($typeName)) {
                return $identifier;
            }
        }
        throw new \Exception(sprintf('No identifier can handle typeof %s', $typeName));
    }

    public function registerSerializer(Serializer $serializer): self
    {
        $this->serializers[] = $serializer;
        return $this;
    }

    public function getSerializerForTypeName(string $typeName): Serializer
    {
        foreach ($this->serializers as $serializer) {
            if ($serializer->canSerialize($typeName)) {
                return $serializer;
            }
        }
        throw new \Exception(sprintf('No serializer can handle typeof %s', $typeName));
    }
}