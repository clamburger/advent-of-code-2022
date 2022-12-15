<?php

namespace App\Day15;

class Sensor {
    public int $distance;

    public function __construct(public int $x, public int $y, public int $beaconX, public int $beaconY)
    {
        $this->distance = abs($beaconX - $x) + abs($this->beaconY - $y);
    }

    public function coveredPositions()
    {
        $minY = $this->y - $this->distance;
        $maxY = $this->y + $this->distance;

        $ys = [];

        for ($y = $minY; $y <= $maxY; $y++) {
            $diff = abs($this->y - $y);

            $reverseDiff = abs($this->distance - $diff);

            $ys[$y] = range($this->x - $reverseDiff, $this->x + $reverseDiff);
        }

        return $ys;
    }

    public function __toString()
    {
        return $this->x . "," . $this->y;
    }
}
