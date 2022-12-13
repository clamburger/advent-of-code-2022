<?php

namespace App\Puzzles;

class Day13DistressSignal extends AbstractPuzzle
{
    protected static int $day_number = 13;

    protected array $packetPairs;

    public function parseInputPartOne()
    {
        foreach ($this->input->lines_by_block as $pair) {
            $packet1 = json_decode($pair[0]);
            $packet2 = json_decode($pair[1]);

            $this->packetPairs[] = [$packet1, $packet2];
        }
    }

    public array $packets;

    public function parseInputPartTwo()
    {
        $this->packets = [];

        foreach ($this->input->lines_by_block as $pair) {
            $packet1 = json_decode($pair[0]);
            $packet2 = json_decode($pair[1]);

            $this->packets[] = $packet1;
            $this->packets[] = $packet2;
        }
    }

    public array $correct = [];

    public function getPartOneAnswer(): int
    {
        $this->parseInputPartOne();

        foreach ($this->packetPairs as $index => $pair) {
//            echo "\n";
//            echo "=== Pair " . $index + 1 .  " ===\n";
            $correct = $this->compareThePair($pair[0], $pair[1]);
            if ($correct) {
                $this->correct[] = $index + 1;
            }
        }

        return array_sum($this->correct);
    }

    public int $indent = 0;

    public function compareThePair(array|int $p1, array|int $p2): ?bool
    {
//        echo "Comparing " . json_encode($p1) . " vs " . json_encode($p2) . "\n";

        if (is_array($p1) && is_array($p2)) {
            $maxLength = max(count($p1), count($p2));

            for ($i = 0; $i <= $maxLength - 1; $i++) {
                $element1 = $p1[$i] ?? null;
                $element2 = $p2[$i] ?? null;

                if ($element1 !== null && $element2 === null) {
//                    echo "  Right side ran out of items, so inputs are NOT in the right order\n";
                    return false;
                } else if ($element1 === null && $element2 !== null) {
//                    echo "  Left side ran out of items, so inputs are in the right order\n";
                    return true;
                }

                $sorted = $this->compareThePair($element1, $element2);

                if ($sorted !== null) {
                    return $sorted;
                }
            }

//            echo "  Comparison did not return\n";
            return null;
        }

        if (is_array($p1) && is_int($p2)) {
//            echo "  Mixed types; convert right to [$p2]\n";
            $p2 = [$p2];
            return $this->compareThePair($p1, $p2);
        }

        if (is_int($p1) && is_array($p2)) {
//            echo "  Mixed types; convert left to [$p1]\n";
            $p1 = [$p1];
            return $this->compareThePair($p1, $p2);
        }

        if (is_int($p1) && is_int($p2)) {
            if ($p1 < $p2) {
//                echo "  Left side is smaller, inputs are in the right order\n";
                return true;
            } else if ($p1 > $p2) {
//                echo "  Right side is smaller, inputs are NOT in the right order\n";
                return false;
            } else {
//                echo "  Inputs equal, no decision\n";
                return null;
            }
        }
    }

    public function getPartTwoAnswer(): int
    {
        $this->parseInputPartTwo();

        $this->packets[] = [[2]];
        $this->packets[] = [[6]];

        usort($this->packets, function ($a, $b) {
            $result = $this->compareThePair($a, $b);
            if ($result === null) {
                return 0;
            } elseif ($result === true) {
                return -1;
            } else {
                return 1;
            }
        });

        $json = array_map(fn ($p) => json_encode($p), $this->packets);

        $a = $b = null;

        foreach ($json as $i => $packet) {
            if ($packet === '[[2]]') {
                $a = $i + 1;
            }
            if ($packet === '[[6]]') {
                $b = $i + 1;
            }
        }

        return $a * $b;
    }
}
