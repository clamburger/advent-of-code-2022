<?php

namespace App\Day18;

class Space
{
    public ?bool $outside = null;

    public bool $cube = false;

    public function __construct(public int $x, public int $y, public int $z)
    {
    }

    public function __toString(): string
    {
        if ($this->outside === true) {
            $id = "◌";
        } elseif ($this->outside === false) {
            $id = "▤";
        } else {
            $id = "?";
        }
        return "$id $this->x,$this->y,$this->z";
    }

    public function neighbourCoords(): array
    {
        return [
            [$this->x - 1, $this->y, $this->z],
            [$this->x + 1, $this->y, $this->z],
            [$this->x, $this->y - 1, $this->z],
            [$this->x, $this->y + 1, $this->z],
            [$this->x, $this->y, $this->z - 1],
            [$this->x, $this->y, $this->z + 1],
        ];
    }

    /**
     * @param Space[][][] $grid
     * @return array<array-key, Space|null>
     */
    public function neighbours(array $grid): array
    {
        return array_map(function ($coords) use ($grid) {
            return $grid[$coords[2]][$coords[1]][$coords[0]] ?? null;
        }, $this->neighbourCoords());
    }
}
