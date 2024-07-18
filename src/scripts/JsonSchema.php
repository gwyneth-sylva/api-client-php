<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Scripts;


require_once 'schema/StandardCompartment.php';
require_once 'schema/StandardSubCompartment.php';
require_once 'schema/StandardInventory.php';
require_once 'schema/StandardPlot.php';
require_once 'schema/StandardRepresentativeTree.php';
require_once 'schema/StandardTreeDistributionStats.php';
require_once 'schema/StandardTree.php';

use ReflectionClass;


function generateJsonSchema($className) {
    $reflector = new ReflectionClass($className);
    $properties = $reflector->getProperties();

    $schema = [
        '$schema' => 'http://json-schema.org/draft-07/schema#',
        'type' => 'object',
        'properties' => []
    ];

    foreach ($properties as $property) {
        $propertyName = $property->getName();
        $propertyType = 'string'; // Default type

        // If the property is an object of another class, generate its schema recursively
        $propertyClass = null;
        if ($property->hasType()) {
            $type = $property->getType();
            if (!$type->isBuiltin()) {
                $propertyClass = $type->getName();
            }
        }

        // Check if the property is an array of objects
        else if (strpos($property->getDocComment(), '@var') !== false) {
            preg_match('/@var\s+(\S+)+(\[\])/', $property->getDocComment(), $matches);
            if (isset($matches[2]) && $matches[2] === '[]') {
                $propertyClass = $matches[1];
                $propertyType = 'array';
                $schema['properties'][$propertyName] = [
                    'type' => 'array',
                    'items' => generateJsonSchema('CloudForest\\ApiClientPhp\\Schema\\'.$propertyClass)
                ];
                continue;
            }
        }


        preg_match('/@var\s+(\S+)/', $property->getDocComment(), $matches2);

        if ($propertyClass) {
            $propertyType = 'object';
            $schema['properties'][$propertyName] = generateJsonSchema($propertyClass);
        }
        elseif(isset($matches2[1]) && class_exists($matches2[1])) {
            $propertyType = $matches2[1];
            $propertyClass = $propertyType;
            $propertyType = 'object';
            $schema['properties'][$propertyName] = generateJsonSchema($propertyClass);
        }
        else {
            if(isset($matches2[1])) {
                $propertyType = $matches2[1];
            }
            $schema['properties'][$propertyName] = [
                'type' => $propertyType
            ];


        }
    }

    return $schema;
}



// Generate the JSON schema for the Person class and print it

// Convert schema to JSON and print it
function printJsonSchema($className) {
    $schema = generateJsonSchema($className);
    echo json_encode($schema, JSON_PRETTY_PRINT);
}

/**
 * Print out a JSON Schema of the spec.
 *
 * @package CloudForest\ApiClientPhp\Scripts
 */
class JsonSchema
{
    /**
     * Run the script.
     * @return void
     */
    public static function run(): void
    {
        $schema = generateJsonSchema("CloudForest\\ApiClientPhp\\Schema\\StandardCompartment");
        echo json_encode($schema, JSON_PRETTY_PRINT);
    }
}

$JsonSchema = new JsonSchema();
$JsonSchema->run();
