<?php

namespace App\Day24;

use App\Puzzles\Day24BlizzardBasin;

class Point
{
    public string $id;
    public int $distanceFromStart = 10000;
    public array $pathFromStart = [];
    public ?int $maybeDistanceToEnd = null;
    public array $blizzards = [];

    public function __construct(public Day24BlizzardBasin $puzzle, public int $x, public int $y, public int $t)
    {
        $this->id = "$x,$y@T+$t";
    }

    public function __clone()
    {
        $this->distanceFromStart = 10000;
        $this->pathFromStart = [];
        $this->maybeDistanceToEnd = null;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function updateT(int $t): void
    {
        $this->t = $t;
        $this->id = "$this->x,$this->y@T+$t";
    }

    public function recalculateDistanceToEnd(): void
    {
        $this->maybeDistanceToEnd = $this->distanceFromStart + $this->hypotheticalDistanceToEnd();
    }

    public function hypotheticalDistanceToEnd(): int
    {
        return abs($this->puzzle->end['x'] - $this->x) + abs($this->puzzle->end['y'] - $this->y);
    }

    public function getCandidates(): array
    {
        $x = $this->x;
        $y = $this->y;
        $t = $this->t;

        $neighbors = [
            ['x' => $x, 'y' => $y + 1, 't' => $t + 1],  // v
            ['x' => $x + 1, 'y' => $y, 't' => $t + 1],  // >
            ['x' => $x, 'y' => $y - 1, 't' => $t + 1],  // ^
            ['x' => $x - 1, 'y' => $y, 't' => $t + 1],  // <
            ['x' => $x, 'y' => $y, 't' => $t + 1],  // wait
        ];

        $neighbors = array_filter($neighbors, function ($pos) {
            $neighbor = $this->puzzle->mapOverTime[$pos['t']][$pos['y']][$pos['x']] ?? null;

            if (!$neighbor || $neighbor === '#') {
                return false;
            }

            if (!empty($neighbor->blizzards)) {
                return false;
            }

            return true;
        });

        return array_map(fn($pos) => $this->puzzle->mapOverTime[$pos['t']][$pos['y']][$pos['x']], $neighbors);
    }

    public function isEnd(): bool
    {
        return $this->x === $this->puzzle->end['x'] && $this->y === $this->puzzle->end['y'];
    }
}
