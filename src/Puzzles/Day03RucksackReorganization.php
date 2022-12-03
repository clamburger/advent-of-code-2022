<?php

namespace App\Puzzles;

class Day03RucksackReorganization extends AbstractPuzzle
{
    protected static int $day_number = 3;

    public function getCompartments(array $grid): array
    {
        $compartment1 = array_splice($grid, count($grid) / 2);
        $compartment2 = $grid;

        return [$compartment1, $compartment2];
    }

    public function priority(string $char): int
    {
        if (preg_match('/[a-z]/', $char)) {
            return ord($char) - 96;
        } else {
            return ord($char) - 38;
        }
    }

    public function getPartOneAnswer(): int
    {
        $score = 0;

        foreach ($this->input->grid as $grid) {
            $bag = $this->getCompartments($grid);

            $diff = array_values(array_intersect($bag[0], $bag[1]));
            $priority = $this->priority($diff[0]);

            $score += $priority;
        }

        return $score;
    }

    public function getPartTwoAnswer(): int
    {
        $groups = array_chunk($this->input->grid, 3);

        $score = 0;

        foreach ($groups as $group) {
            $badge = array_values(array_intersect(...$group))[0];
            $priority = $this->priority($badge);
            $score += $priority;
        }

        return $score;
    }
}
