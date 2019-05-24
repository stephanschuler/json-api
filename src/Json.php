<?php

namespace StephanSchuler\JsonApi;

use Traversable;

class Json
{
    public static function rewrap($subject): array
    {
        return json_decode(
            json_encode(
                $subject,
                JSON_PRETTY_PRINT
            ),
            true
        );
    }

    public static function rewrapTraversable(Traversable $traversable)
    {
        return self::rewrap(iterator_to_array($traversable));
    }
}