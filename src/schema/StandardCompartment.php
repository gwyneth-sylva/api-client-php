<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardCompartment
{
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
     * @var string
     */
    public $boundary;

    /**
     * TODO
     * @var string
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
