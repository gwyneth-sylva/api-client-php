<?php


class StandardCompartment {

    /**
     * ID (guuid)
     * @var string
     */
    public $id;

    /**
     * name
     * @var string;
     */
    public $name = '';

    /**
     * notes
     * @var string;
     */
    public $notes = '';

    /**
     * TODO
     */
    public $boundary;

    /**
     * TODO
     */
    public $centroidcentroid;

    /**
     * subcompartment array
     * @var StandardSubCompartment[]
     */
    public $subCompartments = [];

    /**
     * @see    Create the Compartment.
     * @param  string $id     The compartment ID.
     * @param  string $name     The compartment name.
     * @param  string $notes     The compartment notes.
     * @param  string $boundary The compartment boundary.
     * @param  string $centroid   The compartment centroid.
     * @param  StandardSubCompartment[] $subCompartments    The compartment subcompartments list.
     * @return void
     */
    public function __construct(string $id, string $name, string $notes, string $boundary, string $centroid, array $subCompartments)
    {
        $this->id = $id;
        $this->name = $name;
        $this->notes = $notes;
        $this->boundary = $boundary;
        $this->centroidcentroid = $centroid;
        $this->subCompartments = $subCompartments;
    }
}

Class StandardSubCompartment {

    /**
     * ID (guuid)
     * @var string
     */
    public $id;

    /**
     * notes
     * @var string;
     */
    public $notes = '';

    /**
     * name
     * @var string;
     */
    public $name = '';

    /**
     * inventory array (may be for multiple years or seasons, so use array)
     * @var  StandardInventory[]
     */
    public $inventory = array();

    /**
     * array of ground truth plots
     * @var  StandardPlot[]
     */
    public $plots = array();

    /**
     * @see    Create the SubCompartment.
     * @param  string $id     The SubCompartment ID.
     * @return void
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * creates an inventory from the plot data
     * TODO
     */
    function createSpeciesDetailsFromPlots () {}
}
Class StandardPlot {
    /**
     * id
     * @var string
     */
    public $id = '';
    /**
     * notes
     * @var string
     */
    public $notes = '';
    /**
     * name
     * @var string
     */
    public $name = '';

    /**
     * TODO
     */
    public $centroid;

    /**
     * area
     * @var float
     */
    public $area;

    /**
     * shape
     * @var string
     */
    public $shape;

    /**
     * real trees in this plot
     * @var StandardTree[]
     */
    public $standingTrees = array();

    /**
     * @see    Create the Plot.
     * @param  string $id     The Plot ID.
     * @return void
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
Class StandardInventory {
    /**
     * notes
     * @var string
     */
    public $notes = '';

    /**
     * date
     * @var string
     */
    public $date;

    //single species inventory

    /**
     * method: manual, calculated (blue book), arboreal
     * @var string
     */
    public $method;

    /**
     * species details array
     * @var StandardRepresentativeTree[]
     */
    public $speciesDetails = array();

    /**
     * @see    Create the Inventory.
     * @param  string $id     The Inventory notes.
     * @return void
     */
    public function __construct(string $notes)
    {
        $this->notes = $notes;
    }

}
Class StandardRepresentativeTree {
    /**
     * tree details
     * @var StandardTree
     */
    public $treeDetails;

    /**
     * distribution stats of this tree
     * @var StandardTreeDistributionStats
     */
    public $distributionStats;

    /**
     * trees per hectare
     * @var float
     */
    public $treesPerHa;


    /**
     * trees per hectare
     * @var float
     */
    public $volumePerHa;


    /**
     * @see    Create the API client and its modules.
     * @param  StandardTree $treeDetails     The tree details.
     * @param  StandardTreeDistributionStats $distributionStats The API secret issued by CloudForest.
     * @param  float $treesPerHa   The number of trees per hectare of this species.
     * @return void
     */
    public function __construct(StandardTree $treeDetails, StandardTreeDistributionStats $distributionStats, float $treesPerHa)
    {
        $this->treeDetails = $treeDetails;
        $this->distributionStats = $distributionStats;
        $this->treesPerHa = $treesPerHa;
        $this->volumePerHa = $this->calcVolumePerHa();
    }

    /**
     * Calculate the volume per hectare
     */
    function calcVolumePerHa () {
        return $this->treeDetails->volume * $this->treesPerHa;
    }

}
Class StandardTreeDistributionStats {
    /**
     * height range
     * @var float
     */
    public $heightRange;

    /**
     * dbh range
     * @var float
     */
    public $dbhRange;

    /**
     * height variance
     * @var float
     */
    public $heightVariance;

    /**
     * dbh variance
     * @var float
     */
    public $dbhVariance;

    /**
     * @see    Create the Tree distribution statistics.
     * @param  float $heightRange     The Tree distribution height range.
     * @return void
     */
    public function __construct(float $heightRange)
    {
        $this->heightRange = $heightRange;
    }

}
Class StandardTree {
    /**
     * species ID
     * todo create common list
     * @var integer
     */
    public $speciesId;

    /**
     * height of this tree
     * if this is a representative tree, thisshould correspond to the mean height
     * @var float
     */
    public $height;
    /**
     * dbh of this tree
     * if this is a representative tree, thisshould correspond to the mean dbh
     * @var float
     */
    public $dbh;

    /**
     * volume
     * @var float
     */
    public $volume;

    /**
     * volume calculation method: eg blue book look up
     * @var string
     */
    public $volumeCalculationethod;

    /**
     * @see    Create the Tree Species.
     * @param  string $speciesId     The species ID.
     * @return void
     */
    public function __construct(string $speciesId)
    {
        $this->speciesId = $speciesId;
    }
}



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
                    'items' => generateJsonSchema($propertyClass)
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

// Generate the JSON schema for the StandardRepresentativeTree class and print it
printJsonSchema('StandardCompartment');

