<?php
declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

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
