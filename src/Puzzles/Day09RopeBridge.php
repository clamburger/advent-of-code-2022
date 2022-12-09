<?php

namespace App\Puzzles;

use App\Day09\Rope;

class Day09RopeBridge extends AbstractPuzzle
{
    protected static int $day_number = 9;

    public function getPartOneAnswer(): int
    {
        $distinct = [];

        $head         = new Rope();
        $tail         = new Rope();
        $tail->parent = $head;

//        echo "H: $head->x, $head->y\n";
//        echo "T: $tail->x, $tail->y\n";

        $distinct[] = "$tail->x|$tail->y";

        foreach ($this->input->lines as $line) {
//            echo "==== $line ====\n";

            [$direction, $distance] = explode(' ', $line);

            for ($i = 0; $i < $distance; $i++) {
                $head->applyDir($direction);
//                echo "H: $head->x, $head->y\n";

                $tail->iterate($head);
//                echo "T: $tail->x, $tail->y\n";

                $distinct[] = "$tail->x|$tail->y";
//                echo "----\n";
            }

//            $head->applyLine($line);
//            echo "H: $head->x, $head->y\n";
        }

        $distinct = array_unique($distinct);

        return count($distinct);
    }

    public function getPartTwoAnswer(): int
    {
        $head = new Rope();

        $ropes = [$head];

        for ($i = 0; $i < 9; $i++) {
            $rope         = new Rope();
            $rope->parent = $ropes[$i];

            $ropes[] = $rope;
        }

        $tail = $ropes[9];

        $distinct[] = "$tail->x|$tail->y";

        foreach ($this->input->lines as $line) {
//            echo "==== $line ====\n";

            [$direction, $distance] = explode(' ', $line);

            for ($i = 0; $i < $distance; $i++) {
                $head->applyDir($direction);

                foreach ($ropes as $rope) {
                    $rope->iterate();
                }
//                echo "H: $head->x, $head->y\n";

//                $tail->iterate($head);
//                echo "T: $tail->x, $tail->y\n";

                $distinct[] = "$tail->x|$tail->y";
//                echo "----\n";
            }

//            $this->visualise($ropes);

//            $head->applyLine($line);
//            echo "H: $head->x, $head->y\n";
        }

        $distinct = array_unique($distinct);

        return count($distinct);
    }

    public function visualise(array $ropes)
    {
        for ($y = 5; $y >= 0; $y--) {
            for ($x = 0; $x <= 5; $x++) {
                foreach ($ropes as $index => $rope) {
                    if ($rope->x === $x && $rope->y * -1 === $y) {
                        if ($index === 0) {
                            echo "H";
                        } elseif ($index === count($ropes) - 1) {
                            echo "T";
                        } else {
                            echo $index;
                        }
                        continue 2;
                    }
                }
                echo ".";
            }
            echo "\n";
        }
        echo "\n";
    }
}
