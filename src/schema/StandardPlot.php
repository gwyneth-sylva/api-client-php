<?php
declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

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
