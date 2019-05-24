<?php

use Composer\Autoload\ClassLoader;
use StephanSchuler\JsonApi\Demo\Domain\Ingredient\Ingredient;
use StephanSchuler\JsonApi\Demo\Domain\Ingredient\Relation;
use StephanSchuler\JsonApi\Demo\Domain\Quantity\Countable;
use StephanSchuler\JsonApi\Demo\Domain\Quantity\Uncountable;
use StephanSchuler\JsonApi\Demo\Domain\Recipe;
use StephanSchuler\JsonApi\Demo\Domain\Step\Step;
use StephanSchuler\JsonApi\Demo\Service\ResourceIdentifier;
use StephanSchuler\JsonApi\Demo\Service\ResourceSerializer;
use StephanSchuler\JsonApi\Resolver;
use StephanSchuler\JsonApi\Schema\Document;
use StephanSchuler\JsonApi\Schema\Documents\SingleDocument;
use StephanSchuler\JsonApi\Queue\SerializationQueue;

$autoload = require __DIR__ . '/../vendor/autoload.php';
assert($autoload instanceof ClassLoader);

$autoload->addPsr4('StephanSchuler\\JsonApi\\Demo\\', __DIR__ . '/src');

$paella = require __DIR__ . '/fixture/paella.php';
assert($paella instanceof Recipe);

$document = Document::createSingle(
    (function (): Resolver {
        $resolver = new Resolver();
        $resolver
            ->registerIdentifier($identifier = new ResourceIdentifier($resolver))
            ->registerSerializer(new ResourceSerializer($resolver));
        $identifier
            ->registerResourceClass(Recipe::class)
            ->registerResourceClass(Ingredient::class)
            ->registerResourceClass(Relation::class)
            ->registerResourceClass(Countable::class)
            ->registerResourceClass(Uncountable::class)
            ->registerResourceClass(Step::class);
        return $resolver;
    })()
);

assert($document instanceof SingleDocument);
$document = $document->withSubject($paella);


echo '<pre>';

print_r(
    json_encode(
        new SerializationQueue($document),
        JSON_PRETTY_PRINT
    )
);