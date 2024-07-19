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
use ReflectionNamedType;

/**
 * @param string $className
 * @return mixed[]
 */
function generateJsonSchema(string $className)
{

    $prefix = '';
    $x = strpos($className, 'tandard');
    echo $className . " x" . ($x) . "x***";
    if ($x == 1) {
        $prefix = 'CloudForest\\ApiClientPhp\\Schema\\';
    }
    $className = $prefix . $className;

    if (!class_exists($className)) {
        // echo "Class $className does not exist.";
        // return;
        throw new \InvalidArgumentException("Class $className does not exist.");
    }
    echo $className;

    $reflector = new ReflectionClass($className);
    $properties = $reflector->getProperties();

    $schema = [
        '$schema' => 'http://json-schema.org/draft-07/schema#',
        'type' => 'object',
        'properties' => [],
    ];


    foreach ($properties as $property) {
        $propertyName = $property->getName();
        $propertyType = 'string'; // Default type

        echo $propertyName . "**";
        $look_for_class = [];
        if ($property->getDocComment()) {
            preg_match('/@var\s+(\S+)/', $property->getDocComment(), $look_for_class);
        }

        // If the property is an object of another class, generate its schema recursively
        $propertyClass = null;
        if ($property->hasType()) {
            echo "hasTYpe**";
            $type = $property->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $propertyClass = $type->getName();
            }
        }

        // Check if the property is an array of objects
        elseif ($property->getDocComment() && strpos($property->getDocComment(), '@var') !== false) {
            echo "**hascomment**";
            preg_match('/@var\s+(\S+)+(\[\])/', $property->getDocComment(), $matches);
            if (isset($matches[2]) && $matches[2] === '[]') {
                echo "isset**";
                $propertyClass = $matches[1];
                $propertyType = 'array';
                $schema['properties'][$propertyName] = [
                    'type' => 'array',
                    'items' => generateJsonSchema($propertyClass),
                ];
                continue;
            } elseif (isset($look_for_class[1]) && strpos($look_for_class[1], 'tandard') == 1) {
                echo "I hear***";
                $propertyType = $look_for_class[1];
                $propertyClass = $propertyType;
                $propertyType = 'object';
            }
        }

        // echo "&&&&".print_r($matches2,true)."&&&";
        echo "&&&&" . $look_for_class[1] . "&&&";
        if ($propertyClass) {
            $propertyType = 'object';
            $schema['properties'][$propertyName] = generateJsonSchema($propertyClass);
        } elseif(isset($look_for_class[1]) && class_exists($look_for_class[1])) {
            echo "isclass**";
            $propertyType = $look_for_class[1];
            $propertyClass = $propertyType;
            $propertyType = 'object';
            $schema['properties'][$propertyName] = generateJsonSchema($propertyClass);
        } else {
            if(isset($look_for_class[1])) {
                $propertyType = $look_for_class[1];
            }
            $schema['properties'][$propertyName] = [
                'type' => $propertyType,
            ];


        }
    }

    return $schema;
}

/**
 * Convert schema to JSON and print it
 * @param string $className
 * @return void
 */
function printJsonSchema($className)
{
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
        $schema = generateJsonSchema("StandardCompartment");
        echo json_encode($schema, JSON_PRETTY_PRINT);
    }
}

$JsonSchema = new JsonSchema();
$JsonSchema->run();
