<?php

namespace StephanSchuler\JsonApi\Helper;

class IterationHelper
{
    public static function generateArray(callable $subject)
    {
        return iterator_to_array($subject());
    }
}