<?php

namespace StephanSchuler\JsonApi\Queue;

use JsonSerializable;
use StephanSchuler\JsonApi\Json;
use StephanSchuler\JsonApi\Queue\Arguments\IncludeRelationships\DropAll;
use StephanSchuler\JsonApi\Queue\Arguments\IncludeRelationships\IncludeRelationships;
use StephanSchuler\JsonApi\Schema\Document;
use StephanSchuler\JsonApi\Schema\Identity;

final class SerializationQueue implements JsonSerializable
{
    private static $instance;

    private $propertyPath = [];

    private $stack = [];

    private $document;

    private $includeRelationships;

    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->includeRelationships = new DropAll();
    }

    public function createFromDocument(Document $document)
    {
        return new self($document);
    }

    public function withIncludeRelationshipsArgument(IncludeRelationships $includeRelationships): self
    {
        $clone = clone $this;
        $clone->includeRelationships = $includeRelationships;
        return $clone;
    }

    public static function get(): self
    {
        return self::$instance;
    }

    public function jsonSerialize(): array
    {
        $intance = self::$instance;
        self::$instance = $this;
        try {
            return Json::rewrap(
                $this->document
            );
        } finally {
            self::$instance = $intance;
        }
    }

    public function traversePropertyPath(string $propertyName, callable $scope)
    {
        try {
            $propertyPath = $this->propertyPath;
            $this->propertyPath[] = $propertyName;
            return $scope();
        } finally {
            $this->propertyPath = $propertyPath;
        }
    }

    public function includeInStack(Identity $identity, $subject): Identity
    {
        $identityString = (string)$identity->toString();
        if (!array_key_exists($identityString, $this->stack)) {
            $this->stack[$identityString] = new QueueItem(
                $this->getPropertyPath(),
                $identity,
                $subject
            );
        }
        return $identity;
    }

    public function getStack(): \Traversable
    {
        for (reset($this->stack); current($this->stack); next($this->stack)) {
            yield current($this->stack);
        }
    }

    public function getPropertyPath(): string
    {
        return join('.', $this->propertyPath);
    }

    public function shouldCurrentPropertyPathBeIncluded(): bool
    {
        return $this->includeRelationships->shouldPropertyPathBeIncluded($this->getPropertyPath());
    }
}