<?php

namespace App\Day23;

use App\Puzzles\Day23UnstableDiffusion;

class Elf
{
    public const DIRECTIONS = [
        'N' => ['x' => 0, 'y' => -1],
        'E' => ['x' => 1, 'y' => 0],
        'S' => ['x' => 0, 'y' => 1],
        'W' => ['x' => -1, 'y' => 0],
        'NE' => ['x' => 1, 'y' => -1],
        'SE' => ['x' => 1, 'y' => 1],
        'NW' => ['x' => -1, 'y' => -1],
        'SW' => ['x' => -1, 'y' => 1],
    ];

    public array $proposals = [
        [['N', 'NE', 'NW'], 'N'],
        [['S', 'SE', 'SW'], 'S'],
        [['W', 'NW', 'SW'], 'W'],
        [['E', 'NE', 'SE'], 'E'],
    ];

    public function __construct(public Day23UnstableDiffusion $puzzle, public int $x, public int $y)
    {
    }

    public function delta(string $direction): array
    {
        $delta = self::DIRECTIONS[$direction];
        $x = $this->x + $delta['x'];
        $y = $this->y + $delta['y'];

        return ['x' => $x, 'y' => $y];
    }

    public function proposeAction(): ?array
    {
        $proposal = null;

        $neighbours = array_filter($this->getNeighbours());

        if (!empty($neighbours)) {
            foreach ($this->proposals as $option) {
                foreach ($option[0] as $direction) {
                    if (!empty($neighbours[$direction])) {
                        continue 2;
                    }
                }
                $proposal = $option[1];
                break;
            }
        }

//        if (!empty($this->getNeighboursInDirections(array_keys(self::DIRECTIONS)))) {
//            foreach ($this->proposals as $option) {
//                if (empty($this->getNeighboursInDirections($option[0]))) {
//                    $proposal = $option[1];
//                    break;
//                }
//            }
//        }

        $rotation = array_shift($this->proposals);
        $this->proposals[] = $rotation;

        return $proposal ? $this->delta($proposal) : null;
    }

    public function acceptProposal(array $coords): void
    {
        $this->puzzle->grid[$this->y][$this->x] = null;

        $this->x = $coords['x'];
        $this->y = $coords['y'];

        $this->puzzle->grid[$this->y][$this->x] = $this;
    }

    public function getNeighbours(): array
    {
        $neighbours = [];

        foreach (array_keys(self::DIRECTIONS) as $direction) {
            ['x' => $x, 'y' => $y] = $this->delta($direction);

            $neighbours[$direction] = $this->puzzle->grid[$y][$x] ?? null;
        }

        return $neighbours;
    }

    public function __toString(): string
    {
        return "$this->x, $this->y";
    }
}
