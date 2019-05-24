<?php

namespace StephanSchuler\JsonApi\Queue;

use StephanSchuler\JsonApi\Schema\Identity;

final class QueueItem
{
    private $propertyPath;

    private $identity;

    private $subject;

    public function __construct(string $propertyPath, Identity $identity, $subject)
    {
        $this->propertyPath = $propertyPath;
        $this->identity = $identity;
        $this->subject = $subject;
    }

    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    public function getIdentity(): Identity
    {
        return $this->identity;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}