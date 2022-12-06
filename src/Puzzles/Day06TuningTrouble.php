<?php

namespace App\Puzzles;

use App\Utilities;
use Illuminate\Support\Collection;

class Day06TuningTrouble extends AbstractPuzzle
{
    protected static int $day_number = 6;

    public function getPartOneAnswer(): int
    {
        $chars = $this->input->grid[0];

        for ($i = 0; $i <= $chars->count() - 3; $i++) {
            $string = $chars->slice($i, 4);
            $unique = $string->unique();
            if (count($unique) === 4) {
                break;
            }
        }

        return $i + 4;
    }

    public function getPartTwoAnswer(): int
    {
        $chars = $this->input->grid[0];

        for ($i = 0; $i <= $chars->count() - 13; $i++) {
            $string = $chars->slice($i, 14);
            $unique = $string->unique();
            if (count($unique) === 14) {
                break;
            }
        }

        return $i + 14;
    }
}
