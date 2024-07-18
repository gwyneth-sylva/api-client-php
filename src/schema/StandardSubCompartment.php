<?php
declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

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
