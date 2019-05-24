<?php

use StephanSchuler\JsonApi\Demo\Domain\Ingredient\Ingredient;
use StephanSchuler\JsonApi\Demo\Domain\Quantity\Countable;
use StephanSchuler\JsonApi\Demo\Domain\Quantity\Uncountable;
use StephanSchuler\JsonApi\Demo\Domain\Recipe;

return Recipe::create('Paella mit Pollo Fino und Chorizo')
    ->withSource('https://www.chefkoch.de/rezepte/1987581322168016/Huhn-Chorizo-Paella.html')
    ->withIngredient(
        $huhn = Ingredient::create('Pollo fino (entbeintes Hähnchen)'),
        Countable::createWeight(600, 'g')
    )
    ->withIngredient(
        $chorizo = Ingredient::create('Chorizo'),
        Countable::createWeight(200, 'g')
    )
    ->withIngredient(
        $champinons = Ingredient::create('Champignons'),
        Countable::createWeight(200, 'g')
    )
    ->withIngredient(
        $tomaten = Ingredient::create('Tomaten'),
        Countable::createWeight(700, 'g')
    )
    ->withIngredient(
        $bohnen = Ingredient::create('Bohnen'),
        Countable::createWeight(200, 'g')
    )
    ->withIngredient(
        $paprikaschoten = Ingredient::create('Paprikaschoten'),
        Countable::createQuantity(1)
    )
    ->withIngredient(
        $reis = Ingredient::create('Reis'),
        Countable::createWeight(500, 'g')
    )
    ->withIngredient(
        $brühe = Ingredient::create('Brühe'),
        Countable::createWeight(800, 'ml')
    )
    ->withIngredient(
        $zitronen = Ingredient::create('Zitronensaft'),
        Uncountable::createQuantity()
    )
    ->withIngredient(
        $safran = Ingredient::create('Safran'),
        Countable::createWeight(0.5, 'TL')
    )
    ->withIngredient(
        $rosmarin = Ingredient::create('Rosmarin'),
        Countable::createWeight(1, 'EL')
    )
    ->withIngredient(
        $petersilie = Ingredient::create('Petersilie'),
        Countable::createWeight(1, 'EL')
    )
    ->withIngredient(
        $olivenöl = Ingredient::create('Olivenöl'),
        Countable::createWeight(60, 'ml')
    )
    ->withIngredient(
        $knoblauch = Ingredient::create('Knoblauch'),
        Countable::createWeight(3, 'Zehen')
    )
    ->withIngredient(
        $salzPfeffer = Ingredient::create('Salz und Pfeffer'),
        Uncountable::createQuantity()
    )
    ->withStep(
        'Fleisch und die Chorizo in mindgerechte Stücke schneiden. 2cm große Würfel bieten sich an.',
        $huhn, $chorizo
    )
    ->withStep(
        'Paprika in Streifen schneiden, Champignons dünne Scheiben und die Bohnen in Stücke.',
        $paprikaschoten, $champinons, $bohnen
    )
    ->withStep(
        'Tomaten grob hacken.',
        $tomaten
    )
    ->withStep(
        'Die Brühe erhitzen.',
        $brühe
    )
    ->withStep(
        'Den Safran in etwas Wasser auflösen.',
        $safran
    )
    ->withStep(
        'Das Olivenöl in einer erhitzen.',
        $olivenöl
    )
    ->withStep(
        'Zunächst die Paprika im Olivenöl anbraten, anschließend wieder aus der Pfanne nehmen.',
        $paprikaschoten
    )
    ->withStep(
        'Dann das Huhn anbraten und wieder aus der Pfanne nehmen. Ich versuche dabei, das Huhn auf die Hautseite zu legen und es nicht zu wenden.',
        $huhn
    )
    ->withStep(
        'Nun die Chorizo braten. Hier tritt viel Öl aus. Dann die Wurst wieder aus der Pfanne nehmen.',
        $chorizo
    )
    ->withStep(
        'Die Champignons Öl aus der Wurst anbraten. Dabei gleich Zitronensaft zugeben und den Knoblauch dazu pressen.',
        $champinons, $zitronen, $knoblauch
    )
    ->withStep(
        'Nun die Tomaten und die Paprika wieder in die Pfanne zu den Champignons geben. Die Tomaten sollen weich werden.',
        $tomaten, $paprikaschoten
    )
    ->withStep(
        'Jetzt das Huhn, die Wurst, die Bohnen dazu geben, ebenfalls Rosmarin, Petersilie, Safran und Reis.',
        $huhn, $chorizo, $bohnen, $rosmarin, $petersilie, $safran, $reis
    )
    ->withStep(
        'Mit der heißen Brühe aufgießen und köcheln lassen. Hierbei sollte nicht mehr gerührt werden müssen. Die Hitze muss dementsprechend gering sein.',
        $brühe
    )
    ->withStep(
        'Zum Schluss mit Salz und Pfeffer abschmecken.',
        $salzPfeffer
    )
    ->withStep(
        'Vom Herd nehmen und etwas ruhen lassen.'
    );