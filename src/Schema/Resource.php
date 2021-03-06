<?php

namespace StephanSchuler\JsonApi\Schema;

use JsonSerializable;
use StephanSchuler\JsonApi\Helper\IterationHelper;
use StephanSchuler\JsonApi\Json;
use StephanSchuler\JsonApi\PropertyAccess\Properties;
use StephanSchuler\JsonApi\Queue\SerializationQueue;
use StephanSchuler\JsonApi\Resolver;
use StephanSchuler\JsonApi\Resource\Serializer;

class Resource implements JsonSerializable
{
    private $subject;

    private $resolver;

    public function __construct($subject, Resolver $resolver)
    {
        $this->subject = $subject;
        $this->resolver = $resolver;
    }

    public function getIdentity(): Identity
    {
        return $this
            ->resolver
            ->getIdentifierForSubject($this->subject)
            ->identify($this->subject);
    }

    public function jsonSerialize()
    {
        return Json::rewrapTraversable(
            $this->getIteratorForJsonSerialize()
        );
    }

    protected function getIteratorForJsonSerialize()
    {
        $queue = SerializationQueue::get();
        $propertyPath = $queue->getPropertyPath();

        yield from Json::rewrap($this->getIdentity());
        if ($this->hasAttributes()) {
            yield 'attributes' => IterationHelper::generateArray(function () use ($queue, $propertyPath) {
                foreach ($this->getAttributeAccessors() as $property) {
                    $propertyName = (string)$property;
                    if (!$queue->shouldPropertyBeIncluded($this->getTypeName(), $propertyName)) {
                        continue;
                    }
                    yield (string)$property => $property($this->subject);
                }
            });
        }

        if ($this->hasRelations()) {
            yield 'relationships' => IterationHelper::generateArray(function () use ($queue, $propertyPath) {
                foreach ($this->getSingleRelationAccessors() as $property) {
                    $propertyName = (string)$property;
                    if (!$queue->shouldPropertyBeIncluded($this->getTypeName(), $propertyName)) {
                        continue;
                    }
                    yield (string)$property => $queue->traversePropertyPath($propertyName,
                        function () use ($queue, $property, $propertyName) {
                            return IterationHelper::generateArray(function () use ($queue, $property) {
                                yield 'meta' => null;
                                if ($queue->shouldCurrentPropertyPathBeIncluded()) {
                                    $subject = $property($this->subject);
                                    $identity = $this->getIdentityForSubjectOnStack($subject);
                                    yield 'data' => $identity;
                                }
                            });
                        });
                }
                foreach ($this->getCollectionRelationAccessors() as $property) {
                    $propertyName = (string)$property;
                    if (!$queue->shouldPropertyBeIncluded($this->getTypeName(), $propertyName)) {
                        continue;
                    }
                    yield (string)$property => $queue->traversePropertyPath($propertyName,
                        function () use ($queue, $property, $propertyName) {
                            return IterationHelper::generateArray(function () use ($queue, $property) {
                                yield 'meta' => null;
                                if ($queue->shouldCurrentPropertyPathBeIncluded()) {
                                    $subjects = $property($this->subject);
                                    $identities = array_map((function ($subject) {
                                        return $this->getIdentityForSubjectOnStack($subject);
                                    }), $subjects);
                                    yield 'data' => $identities;
                                }
                            });
                        });
                }
            });
        }

        yield '@propertyPath' => '/' . str_replace('.', '/', $propertyPath);
    }

    protected function hasAttributes()
    {
        $typeName = $this->getTypeName();
        return $this
            ->resolver
            ->getSerializerForTypeName($typeName)
            ->hasAttributes($typeName);
    }

    protected function getAttributeAccessors(): Properties
    {
        $typeName = $this->getTypeName();
        return $this
            ->resolver
            ->getSerializerForTypeName($typeName)
            ->getAttributeAccessors($typeName);
    }

    protected function hasRelations()
    {
        $typeName = $this->getTypeName();
        return $this
            ->getSerialize()
            ->hasRelations($typeName);
    }

    protected function getSingleRelationAccessors(): Properties
    {
        $typeName = $this->getTypeName();
        return $this
            ->getSerialize()
            ->getSingleRelationAccessors($typeName);
    }

    protected function getCollectionRelationAccessors(): Properties
    {
        $typeName = $this->getTypeName();
        return $this
            ->getSerialize()
            ->getCollectionRelationAccessors($typeName);
    }

    protected function getSerialize(): Serializer
    {
        $typeName = $this->getTypeName();
        return $this
            ->resolver
            ->getSerializerForTypeName($typeName);
    }

    protected function getTypeName(): string
    {
        return $this->getIdentity()->getTypeName();
    }

    protected function getIdentityForSubjectOnStack($subject): Identity
    {
        $identity = $this->resolver->getIdentifierForSubject($subject)->identify($subject);
        return SerializationQueue::get()->includeInStack($identity, $subject);
    }
}