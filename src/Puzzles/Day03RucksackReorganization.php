<?php

namespace App\Puzzles;

use Illuminate\Support\Collection;

class Day03RucksackReorganization extends AbstractPuzzle
{
    protected static int $day_number = 3;

    /**
     * @param Collection $grid
     * @return Collection<Collection>
     */
    public function getCompartments(Collection $grid): Collection
    {
        $grid = clone $grid;
        $compartment1 = $grid->splice(count($grid) / 2);
        $compartment2 = $grid;

        return collect([$compartment1, $compartment2]);
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

            $diff = $bag[0]->intersect($bag[1])->values();
            $priority = $this->priority($diff[0]);

            $score += $priority;
        }

        return $score;
    }

    public function getPartTwoAnswer(): int
    {
        $groups = $this->input->grid->chunk(3);

        $score = 0;


        foreach ($groups as $group) {
            $badge = Collection::intersectAll($group)->first();
            $priority = $this->priority($badge);
            $score += $priority;
        }

        return $score;
    }
}
