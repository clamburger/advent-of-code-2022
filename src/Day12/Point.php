<?php

namespace App\Day12;

use App\Puzzles\Day12HillClimbingAlgorithm;

class Point {
    public string $id;
    public int $distanceFromStart = 10000;
    public array $pathFromStart = [];
    public int $maybeDistanceToEnd;

    public function __construct(public Day12HillClimbingAlgorithm $puzzle, public int $x, public int $y, public int $height)
    {
        $this->id = "$x,$y";
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function recalculateDistanceToEnd()
    {
        $this->maybeDistanceToEnd = $this->distanceFromStart + $this->puzzle->hypotheticalDistanceToEnd($this->x, $this->y);
    }
}
