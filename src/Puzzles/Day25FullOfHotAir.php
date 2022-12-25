<?php

namespace App\Puzzles;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;

class Day25FullOfHotAir extends AbstractPuzzle
{
    protected static int $day_number = 25;

    /** @var Collection<Stringable> */
    public Collection $fuel;

    public function snafuToDecimal(string $snafu): int
    {
        $chars = array_reverse(str_split($snafu));

        $decimal = 0;

        foreach ($chars as $magnitude => $char) {
            if ($char === '-') {
                $digit = -1;
            } elseif ($char === '=') {
                $digit = -2;
            } else {
                $digit = (int)$char;
            }

            $decimal += $digit * (5 ** $magnitude);
        }

        return $decimal;
    }

    public function decimalToSnafu(int $decimal): string
    {
        $base5 = base_convert($decimal, 10, 5);

        $places = [];

        $chars = array_reverse(str_split($base5));

        foreach ($chars as $magnitude => $char) {
            // Carry over
            if (isset($places[$magnitude])) {
                $char = (string)((int)$char + 1);
            }


            if ($char === '0' || $char === '1' || $char === '2') {
                $places[$magnitude] = $char;
            }

            if ($char === '3') {
                $places[$magnitude] = '=';
                $places[$magnitude + 1] = '1';
            }

            if ($char === '4') {
                $places[$magnitude] = '-';
                $places[$magnitude + 1] = '1';
            }
        }

        return implode('', array_reverse($places));
    }

    public function parseInput()
    {
        $this->fuel = $this->input->lines;
    }

    public function getPartOneAnswer(): string
    {
        $this->parseInput();

//        foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 15, 20, 2022, 12345, 314159265] as $num) {
//            echo "$num = " . $this->decimalToSnafu($num) . "\n";
//        }

        $number = 0;
        foreach ($this->fuel as $snafu) {
            $number += $this->snafuToDecimal($snafu);
        }

        return $this->decimalToSnafu($number);
    }

    public function getPartTwoAnswer(): string
    {
        return 'Start the blender';
    }
}
