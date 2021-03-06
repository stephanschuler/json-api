<?php

namespace StephanSchuler\JsonApi\Schema;

use StephanSchuler\JsonApi\Helper\IterationHelper;
use StephanSchuler\JsonApi\Json;
use StephanSchuler\JsonApi\Queue\QueueItem;
use StephanSchuler\JsonApi\Queue\SerializationQueue;
use StephanSchuler\JsonApi\Resolver;
use StephanSchuler\JsonApi\Schema\Documents\CollectionDocument;
use StephanSchuler\JsonApi\Schema\Documents\SingleDocument;

abstract class Document
{
    private $resolver;

    private function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public static function createSingle(Resolver $resolver): SingleDocument
    {
        return new SingleDocument($resolver);
    }

    public static function createCollection(Resolver $resolver): CollectionDocument
    {
        return new CollectionDocument($resolver);
    }

    public function jsonSerialize()
    {
        return IterationHelper::generateArray(function () {

            yield from Json::rewrapTraversable(
                $this->getIteratorForJsonSerialize()
            );

            yield 'included' => IterationHelper::generateArray(function () {
                foreach (SerializationQueue::get()->getStack() as $item) {
                    assert($item instanceof QueueItem);
                    yield SerializationQueue::get()->traversePropertyPath($item->getPropertyPath(),
                        function () use ($item) {
                            return Json::rewrap(
                                new Resource($item->getSubject(), $this->resolver)
                            );
                        });
                }
            });
        });
    }

    protected function getIteratorForJsonSerialize()
    {
        if ($this->hasData()) {
            yield 'data' => $this->getData();
        }
        if ($this->hasMeta()) {
            yield 'meta' => $this->getMeta();
        }
        if ($this->hasLinks()) {
            yield 'links' => $this->getLinks();
        }
    }

    abstract protected function hasData(): bool;

    abstract protected function getData();

    abstract protected function hasMeta(): bool;

    abstract protected function getMeta();

    abstract protected function hasLinks(): bool;

    abstract protected function getLinks();

    protected function getResolver(): Resolver
    {
        return $this->resolver;
    }
}