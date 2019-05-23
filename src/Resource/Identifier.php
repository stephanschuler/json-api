<?php

namespace StephanSchuler\JsonApi\Resource;

use StephanSchuler\JsonApi\Schema\Identity;

interface Identifier extends Handler
{
    public function identify($subject): Identity;

    public function resolvesTypeNameForClassName(string $className): bool;

    public function getTypeNameForClassName(string $className): string;

    public function resolvesClassNameForTypeName(string $typeName): bool;

    public function getClassNameForTypeName(string $typeName): string;
}