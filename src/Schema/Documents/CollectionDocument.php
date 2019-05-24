<?php

namespace StephanSchuler\JsonApi\Schema\Documents;

use StephanSchuler\JsonApi\JsonSerializableTraversable;
use StephanSchuler\JsonApi\Schema\Document;

class CollectionDocument extends Document implements JsonSerializableTraversable
{
    private $subject;

    public function withSubjects(... $subject): self
    {
        $clone = clone $this;
        $clone->subject = $subject;
        return $clone;
    }

    protected function hasData(): bool
    {
        return true;
    }

    protected function getData()
    {
        if (!$this->subject) {
            return [];
        }
        return array_map(function ($subject) {
            return Resource($subject);
        }, $this->subject);
    }

    protected function hasMeta(): bool
    {
        return false;
    }

    protected function getMeta()
    {
        return null;
    }

    protected function hasLinks(): bool
    {
        return false;
    }

    protected function getLinks()
    {
        return null;
    }
}