<?php

namespace App\Puzzles;

use App\Day23\Elf;

class Day23UnstableDiffusion extends AbstractPuzzle
{
    protected static int $day_number = 23;

    public array $grid;

    /** @var Elf[] */
    public array $elves;

    public function parseInput()
    {
        $this->grid = $this->input->grid->toArray();
        $this->elves = [];

        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $char) {
                if ($char === '#') {
                    $elf = new Elf($this, $x, $y);
                    $this->elves[] = $elf;
                    $this->grid[$y][$x] = $elf;
                } else {
                    $this->grid[$y][$x] = null;
                }
            }
        }
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput();

//        echo "== Initial State ==\n";
//        $this->showMap();

        $i = 0;

        while (true) {
            $i++;
            $proposals = [];

            // First half
            foreach ($this->elves as $elf) {
                $proposal = $elf->proposeAction();
                if ($proposal) {
                    $proposals["{$proposal['x']},{$proposal['y']}"][] = [$elf, $proposal];
                }
            }

            // Second half
            foreach ($proposals as $proposal) {
                if (count($proposal) > 1) {
                    continue;
                }

                /** @var \App\Day23\Elf $elf */
                $elf = $proposal[0][0];
                $elf->acceptProposal($proposal[0][1]);
            }

//            echo "== End of Round $i ==\n";
//            $this->showMap();

            if (empty($proposals) || $i === 10) {
                break;
            }
        }

        $boundaries = $this->getElfBoundaries();

        $squares = ($boundaries['maxX'] - $boundaries['minX'] + 1) * ($boundaries['maxY'] - $boundaries['minY'] + 1);

        return $squares - count($this->elves);
    }

    public function showMap()
    {
        $boundaries = $this->getElfBoundaries();

        for ($y = $boundaries['minY'] - 1; $y <= $boundaries['maxY'] + 1; $y++) {
            for ($x = $boundaries['minX'] - 1; $x <= $boundaries['maxX'] + 1; $x++) {
                $square = $this->grid[$y][$x] ?? null;

                if ($square) {
                    echo "#";
                } else {
                    echo ".";
                }
            }
            echo "\n";
        }
        echo "\n\n";
    }

    public function getElfBoundaries(): array
    {
        $minX = $this->elves[0]->x;
        $maxX = $this->elves[0]->x;
        $minY = $this->elves[0]->y;
        $maxY = $this->elves[0]->y;

        foreach ($this->elves as $elf) {
            $minX = min($minX, $elf->x);
            $maxX = max($maxX, $elf->x);
            $minY = min($minY, $elf->y);
            $maxY = max($maxY, $elf->y);
        }

        return [
            'minX' => $minX,
            'maxX' => $maxX,
            'minY' => $minY,
            'maxY' => $maxY,
        ];
    }

    public function getPartTwoAnswer(): int
    {
        $this->parseInput();

//        echo "== Initial State ==\n";
//        $this->showMap();

        $i = 0;

        while (true) {
            $i++;
            $proposals = [];

            // First half
            foreach ($this->elves as $elf) {
                $proposal = $elf->proposeAction();
                if ($proposal) {
                    $proposals["{$proposal['x']},{$proposal['y']}"][] = [$elf, $proposal];
                }
            }

            // Second half
            foreach ($proposals as $proposal) {
                if (count($proposal) > 1) {
                    continue;
                }

                $proposal[0][0]->acceptProposal($proposal[0][1]);
            }

//            if ($i % 100 === 0) {
//                echo "== End of Round $i ==\n";
//                $this->showMap();
//            }

            if (empty($proposals)) {
                break;
            }
        }

        return $i;
    }
}
