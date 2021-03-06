<?php

namespace StephanSchuler\JsonApi\Schema\Documents;

use JsonSerializable;
use StephanSchuler\JsonApi\Schema\Document;
use StephanSchuler\JsonApi\Schema\Resource;

class SingleDocument extends Document implements JsonSerializable
{
    private $subject;

    public function withSubject($subject): self
    {
        $clone = clone $this;
        $clone->subject = $subject;
        return $clone;
    }

    public function withoutSubject(): self
    {
        $clone = clone $this;
        $clone->subject = null;
        return $clone;
    }

    protected function hasData(): bool
    {
        return true;
    }

    /**
     * @return Resource|null
     */
    protected function getData()
    {
        if ($this->subject) {
            return new Resource($this->subject, $this->getResolver());
        }
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