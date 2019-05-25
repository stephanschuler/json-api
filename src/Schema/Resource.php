<?php

namespace StephanSchuler\JsonApi\Schema;

use StephanSchuler\JsonApi\Helper\IterationHelper;
use StephanSchuler\JsonApi\Json;
use StephanSchuler\JsonApi\JsonSerializableTraversable;
use StephanSchuler\JsonApi\Queue\SerializationQueue;
use StephanSchuler\JsonApi\Resolver;
use StephanSchuler\JsonApi\Resource\Serializer;

class Resource implements JsonSerializableTraversable
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
            $this->getIterator()
        );
    }

    public function getIterator()
    {
        $propertyPath = SerializationQueue::get()->getPropertyPath();
        yield 'propertyPath' => $propertyPath;

        yield from $this->getIdentity();
        if ($this->hasAttributes()) {
            yield 'attributes' => IterationHelper::generateArray(function () use ($propertyPath) {
                foreach ($this->getAttributeAccessors() as $propertyName => $accessor) {
                    yield $propertyName => $accessor($this->subject);
                }
            });
        }

        if ($this->hasRelations()) {
            yield 'relationships' => IterationHelper::generateArray(function () use ($propertyPath) {
                foreach ($this->getSingleRelationAccessors() as $propertyName => $accessor) {
                    yield from $this->yieldTraversedPropertyPath($propertyName,
                        function () use ($accessor, $propertyName) {
                            return IterationHelper::generateArray(function () use ($accessor) {
                                yield 'meta' => null;
                                if (SerializationQueue::get()->shouldCurrentPropertyPathBeIncluded()) {
                                    $subject = $accessor($this->subject);
                                    $identity = $this->getIdentityForSubjectOnStack($subject);
                                    yield 'data' => $identity;
                                }
                            });
                        });
                }
                foreach ($this->getCollectionRelationAccessors() as $propertyName => $accessor) {
                    yield from $this->yieldTraversedPropertyPath($propertyName,
                        function () use ($accessor, $propertyName) {
                            return IterationHelper::generateArray(function () use ($accessor) {
                                yield 'meta' => null;
                                if (SerializationQueue::get()->shouldCurrentPropertyPathBeIncluded()) {
                                    $subjects = $accessor($this->subject);
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
    }

    protected function hasAttributes()
    {
        $typeName = $this->getTypeName();
        return $this
            ->resolver
            ->getSerializerForTypeName($typeName)
            ->hasAttributes($typeName);
    }

    protected function getAttributeAccessors()
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

    protected function getSingleRelationAccessors()
    {
        $typeName = $this->getTypeName();
        return $this
            ->getSerialize()
            ->getSingleRelationAccessors($typeName);
    }

    protected function getCollectionRelationAccessors()
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

    protected function yieldTraversedPropertyPath(string $propertyName, callable $callable)
    {
        $value = SerializationQueue::get()->traversePropertyPath($propertyName, $callable);
        yield $propertyName => $value;
    }
}