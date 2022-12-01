<?php

namespace App\Puzzles;

class Day01CalorieCount extends AbstractPuzzle
{
    protected static int $day_number = 1;

    /**
     * @return int
     */
    public function getPartOneAnswer(): int
    {
        $inventories = $this->input->raw_blocks;

        $inventories = array_map(fn ($i) => explode("\n", $i), $inventories);

        $sum = array_map(fn ($i) => array_sum($i), $inventories);
        return max(...$sum);
    }

    /**
     * @return int
     */
    public function getPartTwoAnswer(): int
    {
        $inventories = $this->input->raw_blocks;

        $inventories = array_map(fn ($i) => explode("\n", $i), $inventories);

        $totals = array_map(fn ($i) => array_sum($i), $inventories);
        rsort($totals);

        return $totals[0] + $totals[1] + $totals[2];
    }
}
