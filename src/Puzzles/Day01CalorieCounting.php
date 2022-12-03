<?php

namespace App\Puzzles;

class Day01CalorieCounting extends AbstractPuzzle
{
    protected static int $day_number = 1;

    /**
     * @return int
     */
    public function getPartOneAnswer(): int
    {
        $inventories = $this->input->raw_blocks;

        $inventories = $inventories->map(fn ($i) => $i->explode("\n"));

        $sum = $inventories->map->sum();
        return max(...$sum);
    }

    /**
     * @return int
     */
    public function getPartTwoAnswer(): int
    {
        $inventories = $this->input->raw_blocks;

        $inventories = $inventories->map(fn ($i) => $i->explode("\n"));

        $totals = $inventories->map->sum()->sortDesc()->values();

        return $totals[0] + $totals[1] + $totals[2];
    }
}
