<?php

namespace StephanSchuler\JsonApi\Schema;

use StephanSchuler\JsonApi\Json;
use StephanSchuler\JsonApi\JsonSerializableTraversable;
use StephanSchuler\JsonApi\Resolver;
use StephanSchuler\JsonApi\Resource\Serializer;
use StephanSchuler\JsonApi\Queue\SerializationQueue;

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
            yield 'attributes' => array_map(
                function (callable $accessor) {
                    return $accessor($this->subject);
                },
                iterator_to_array(
                    $this->getAttributeAccessors()
                ));
        }

        if ($this->hasRelations()) {
            yield 'relations' => iterator_to_array((function () {
                foreach ($this->getSingleRelationAccessors() as $propertyName => $accessor) {
                    yield from $this->yieldTraversedPropertyPath($propertyName,
                        function () use ($accessor) {
                            $subject = $accessor($this->subject);
                            $identity = $this->getIdentityForSubjectOnStack($subject);
                            return [
                                'data' => $identity
                            ];
                        });
                }
                foreach ($this->getCollectionRelationAccessors() as $propertyName => $accessor) {
                    yield from $this->yieldTraversedPropertyPath($propertyName,
                        function () use ($accessor) {
                            $subjects = $accessor($this->subject);
                            $identities = array_map((function ($subject) {
                                return $this->getIdentityForSubjectOnStack($subject);
                            }), $subjects);
                            return [
                                'data' => $identities
                            ];
                        });
                }
            })());
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
        yield $propertyName => SerializationQueue::get()->traversePropertyPath($propertyName, $callable);
    }
}