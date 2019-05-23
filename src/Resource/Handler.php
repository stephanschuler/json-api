<?php

namespace StephanSchuler\JsonApi\Resource;

use StephanSchuler\JsonApi\Resolver;

interface Handler
{
    public function __construct(Resolver $resolver);
}