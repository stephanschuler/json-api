<?php

namespace StephanSchuler\JsonApi;

use IteratorAggregate;
use JsonSerializable;

interface JsonSerializableTraversable extends JsonSerializable, IteratorAggregate
{
}