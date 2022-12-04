<?php

namespace App\Puzzles;

class Day04CampCleanup extends AbstractPuzzle
{
    protected static int $day_number = 4;

    public function getPartOneAnswer(): int
    {
        $ranges = $this->input->lines->map(fn ($line) => explode(",", $line));

        $overlaps = 0;


        foreach ($ranges as $pair) {
            $pair_array = collect($pair)->map(fn ($p) => explode("-", $p));

            $a = $pair_array[0];
            $b = $pair_array[1];

            if ($a[0] <= $b[0] && $a[1] >= $b[1]) {
//                echo "A $a[0]-$a[1] contains $b[0]-$b[1]\n";
                $overlaps++;
                continue;
            }

            if ($b[0] <= $a[0] && $b[1] >= $a[1]) {
//                echo "B $b[0]-$b[1] contains $a[0]-$a[1]\n";
                $overlaps++;
                continue;
            }
        }

        return $overlaps;
    }

    public function getPartTwoAnswer(): int
    {
        $ranges = $this->input->lines->map(fn ($line) => explode(",", $line));

        $overlaps = 0;

        foreach ($ranges as $pair) {
            $pair_array = collect($pair)->map(fn ($p) => explode("-", $p));

            $pair_array = $pair_array->sort(function ($a, $b) {
                $r = (int)$a[0] <=> (int)$b[0];
                return $r === 0 ? (int)$a[1] <=> (int)$b[1] : $r;
            })->values();

            $a = $pair_array[0];
            $b = $pair_array[1];

            if ($a[1] >= $b[0]) {
//                echo "A $a[0]-$a[1] overlaps $b[0]-$b[1]\n";
                $overlaps++;
            } else {
//                echo "C $a[0]-$a[1] does not overlap $b[0]-$b[1]\n";
            }
        }

        return $overlaps;
    }
}
