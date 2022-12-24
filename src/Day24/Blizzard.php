<?php

namespace App\Day24;

use App\Puzzles\Day24BlizzardBasin;

class Blizzard
{
    public static int $idCounter = 0;

    public int $id;

    public function __construct(
        public Day24BlizzardBasin $puzzle,
        public int $x,
        public int $y,
        public string $direction
    ) {
        $this->id = self::$idCounter;
        self::$idCounter++;
    }

    public function move(): void
    {
        $x = $this->x;
        $y = $this->y;

        if ($this->direction === '>') {
            $x = $this->x + 1;
        } elseif ($this->direction === '<') {
            $x = $this->x - 1;
        } elseif ($this->direction === 'v') {
            $y = $this->y + 1;
        } else {
            $y = $this->y - 1;
        }

        if ($x === 0) {
            $x = $this->puzzle->width - 2;
        } elseif ($x === $this->puzzle->width - 1) {
            $x = 1;
        }

        if ($y === 0) {
            $y = $this->puzzle->height - 2;
        } elseif ($y === $this->puzzle->height - 1) {
            $y = 1;
        }

        unset($this->puzzle->map[$this->y][$this->x]->blizzards[$this->id]);

        $this->x = $x;
        $this->y = $y;

        $this->puzzle->map[$this->y][$this->x]->blizzards[$this->id] = $this;
    }
}
