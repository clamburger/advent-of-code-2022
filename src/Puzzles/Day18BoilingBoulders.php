<?php

namespace App\Puzzles;

use App\Day18\Cube;
use App\Day18\Space;

class Day18BoilingBoulders extends AbstractPuzzle
{
    protected static int $day_number = 18;

    /** @var Cube[] */
    public array $cubes;

    /** @var Space[][][]  */
    public array $grid;

    /** @var Space[] */
    public array $unknown;

    /** @var Space[] */
    public array $outsideAir;

    public int $max;

    public int $min = -1;

    public function parseInput()
    {
        $this->grid = $this->cubes = [];
        $this->max = 0;

        $cubes = $this->input->lines->map(fn ($line) => explode(',', $line));

        foreach ($cubes as $cube) {
            $cube = new Cube(...$cube);
            $this->cubes[] = $cube;
            $this->grid[$cube->z][$cube->y][$cube->x] = $cube;

            $this->max = max($this->max, $cube->x, $cube->y, $cube->z);
        }

        $this->max++;
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput();

        $sides = 0;

        foreach ($this->cubes as $cube) {
            foreach ($cube->neighbourCoords() as $coords) {
                if (!$this->cubeAt(...$coords)) {
                    $sides++;
                }
            }
        }

        return $sides;
    }

    public function cubeAt(int $x, int $y, int $z)
    {
        return ($this->grid[$z][$y][$x] ?? null) instanceof Cube;
    }

    /**
     * For each space that we don't know is inside or outside, check to see if any of the neighbours are outside.
     * If they are, the space gets updated and removed from the unknown set.
     */
    public function sweepUnknownAir(): void
    {
        $unknown = [];

        foreach ($this->unknown as $space) {
            foreach ($space->neighbours($this->grid) as $neighbour) {
                if ($neighbour->outside) {
                    $space->outside = true;
                    $this->outsideAir[] = $space;

                    continue 2;
                }
            }
            $unknown[] = $space;
        }

        $this->unknown = $unknown;
    }

    public function getPartTwoAnswer(): int
    {
        $this->parseInput();
        $this->populateGrid();

        // Arbitrary amount of iterations chosen here
        for ($i = 0; $i < 10; $i++) {
            $this->sweepUnknownAir();
        }

        $sides = 0;

        foreach ($this->outsideAir as $air) {
            foreach ($air->neighbours($this->grid) as $neighbour) {
                if ($neighbour?->cube) {
                    $sides++;
                }
            }
        }

        return $sides;
    }

    public function populateGrid()
    {
        // Good thing the grid is small.

        for ($z = $this->min; $z <= $this->max; $z++) {
            for ($y = $this->min; $y <= $this->max; $y++) {
                for ($x = $this->min; $x <= $this->max; $x++) {
                    if (!$this->cubeAt($x, $y, $z)) {
                        $space = new Space($x, $y, $z);

                        if (max($x, $y, $z) === $this->max || min($x, $y, $z) === $this->min) {
                            $space->outside = true;
                            $this->outsideAir[] = $space;
                        } else {
                            $this->unknown[] = $space;
                        }

                        $this->grid[$z][$y][$x] = $space;
                    }
                }
                ksort($this->grid[$z][$y]);
            }
            ksort($this->grid[$z]);
        }
        ksort($this->grid);
    }
}
