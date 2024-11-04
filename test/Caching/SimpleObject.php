<?php

namespace Kinikit\Core\Caching;

class SimpleObject {

    private string $colour;

    public function __construct($colour = "pink") {
        $this->colour = $colour;
    }

    public function getColour(): string {
        return $this->colour;
    }

    public function setColour(string $colour): void {
        $this->colour = $colour;
    }

}