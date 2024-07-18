<?php
declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

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

