<?php

namespace App\Puzzles;

use App\Day17\Rock;

class Day17PyroclasticFlow extends AbstractPuzzle
{
    protected static int $day_number = 17;

    public array $chamber = [];

    public array $instructions = [];

    public int $knownHighest = -1;

    public function visualise(?Rock $rock = null, ?string $lastInstruction = null)
    {
        $output = [];
        $output[] = '+-------+';

        foreach ($this->chamber as $y => $contents) {
            $row = [];
            $row[] = '|';

            ksort($contents);

            foreach ($contents as $x => $value) {

                while (count($row) < $x + 1) {
                    $row[] = ' ';
                }

                if ($rock?->on($x, $y)) {
                    if ($value) {
                        throw new \Exception('Falling rock intersecting standing rock');
                    }
                    $row[] = $this->colour('@', 31);
                } else if ($value) {
                    $row[] = $this->colour('#', 32);
                } else {
                    $row[] = $this->colour('.', 37);
                }
            }

            while (count($row) < 8) {
                $row[] = ' ';
            }

            $row[] = '| ' . $y;

            if ($rock && $y === $rock->y && $lastInstruction) {
                $row[] .= ' ' . $lastInstruction;
            }

            $output[] = implode('', $row);
        }


        echo implode("\n", array_reverse($output)) . "\n";
    }

    public function highestLine(): int
    {
        return $this->knownHighest;
    }

    private function colour(string $string, int $colour): string
    {
        return "\033[{$colour}m" . $string . "\033[0m";
    }

    public function getInstruction(int $i): string
    {
        return $this->instructions[$i % count($this->instructions)];
    }

    public function getPartOneAnswer(): int
    {
        $this->chamber = [];
        $this->instructions = $this->input->grid[0]->toArray();

        $instructionIndex = 0;

        for ($i = 0; $i < 2001; $i++) {
            $rock = new Rock($this, $i);

//            $this->visualise($rock, 'New');

            while (!$rock->stopped) {

                $instruction = $this->getInstruction($instructionIndex);
                $instructionIndex++;

                if ($instruction === '<') {
                    $rock->left();
                } elseif ($instruction === '>') {
                    $rock->right();
                }

//                $this->visualise($rock, $instruction);

                $rock->down();

//                $this->visualise($rock, 'v');

            }
        }

//        $this->visualise($rock);

        return $this->highestLine() + 1;
    }

    public function getSimpleChamber(): array
    {
        $lines = [];

        foreach ($this->chamber as $y => $contents) {
            $row = [];
            $row[] = '|';

            ksort($contents);

            foreach ($contents as $x => $value) {

                while (count($row) < $x + 1) {
                    $row[] = ' ';
                }

                if ($value) {
                    $row[] = '█';
                } else {
                    $row[] = ' ';
                }
            }

            while (count($row) < 8) {
                $row[] = ' ';
            }

            $row[] = '|';
            $lines[] = implode('', $row);
        }

        return $lines;
    }

    public function getDistinctRows(): array
    {
        $chamber = $this->getSimpleChamber();

        $lines = [];

        foreach ($chamber as $line) {
            if (!isset($lines[$line])) {
                $lines[$line] = 0;
            }
            $lines[$line]++;
        }

        return $lines;
    }

    // Properties for Part 2

    public string $lineCycle;
    public int $lineCycleLength;
    public int $lineCycleStart;
    public int $rockCycleStart;
    public int $rockCycleLength;
    public int $heightAddedEachCycle;
    public int $heightUpToStartOfFirstCycle;
    public int $lastCycleFound = 0;

    public function getPartTwoAnswer(): int
    {
        $this->instructions = $this->input->grid[0]->toArray();

        if (count($this->instructions) < 100) {
            $this->lineCycle = '0 1 2 1 2 18 18 5 5 5 5 19 20 21 20 22 23 24 0 25 26 27 28 28 3 13 29 5 2 18 18 5 5 5 5 19 30 31 32 9 9 19 33 34 34 35 35 19 36 37 0 2 2';
            $this->lineCycleLength = 53;
            $this->lineCycleStart = 25;

            $this->rockCycleStart = 14; // 14, 49, 84, 119, 154, 189, 224
            $this->rockCycleLength = 35;

            $this->heightUpToStartOfFirstCycle = 24;
            $this->heightAddedEachCycle = 53;
        } else {
            $this->lineCycle = '45 45 18 18 18 18 10 57 44 41 41 8 8 29 3 4 26 59 60 52 52 23 61 24 19 62 62 62 19 19 0 18 24 18 40 63 64 26 65 0 4 4 15 1 2 3 4 5 5 3 3 2 39 48 0 48 58 44 62 40 29 41 41 8 8 9 9 25 3 4 3 4 5 5 3 3 9 9 15 8 33 29 44 66 0 45 53 53 0 45 45 18 18 18 18 0 18 24 0 3 3 9 9 25 3 4 25 67 67 2 54 15 55 20 18 18 19 19 0 18 24 0 15 15 16 54 68 61 69 57 57 48 39 48 43 70 70 62 62 25 3 4 3 4 45 45 25 71 6 3 4 41 41 8 8 2 24 18 4 45 45 18 18 18 18 29 72 29 72 29 5 5 3 3 9 9 10 44 46 10 73 74 52 75 76 44 77 78 40 29 5 5 3 3 6 33 9 15 9 9 9 9 15 35 34 0 18 18 18 18 18 18 19 19 0 3 4 3 33 42 42 35 35 79 79 0 37 69 37 42 37 42 0 26 50 80 35 34 35 34 8 8 8 8 8 8 9 9 25 81 67 80 82 34 44 78 40 29 41 41 8 8 15 1 2 3 4 41 41 41 29 2 0 0 0 37 35 34 2 61 61 83 79 79 10 25 72 25 81 84 5 29 3 4 3 4 45 45 58 58 18 18 29 3 4 25 81 81 3 3 29 12 14 30 44 18 18 18 18 18 18 29 3 4 15 6 8 36 33 0 42 0 48 18 19 19 0 3 4 6 7 7 7 85 85 26 15 42 15 9 8 9 9 10 44 46 62 19 19 29 3 4 24 10 42 42 10 43 37 69 46 62 29 72 29 72 14 3 3 9 33 69 10 19 19 62 19 19 29 3 4 3 4 41 41 8 8 36 36 29 8 33 36 0 48 48 19 19 0 3 4 3 33 42 42 33 33 0 18 24 0 42 42 38 38 10 11 30 30 30 52 52 23 61 24 2 53 73 86 68 68 23 3 4 3 4 5 5 3 3 9 9 0 43 82 87 0 39 48 35 15 2 58 18 18 18 18 18 0 3 4 3 4 5 5 3 3 9 9 29 72 29 72 29 5 5 3 3 9 9 29 71 6 72 14 72 72 15 8 33 8 34 8 8 9 9 0 39 30 14 33 42 0 42 15 24 72 72 3 3 9 9 29 14 51 86 18 18 18 29 30 31 10 0 36 8 8 29 8 33 29 13 13 88 6 13 31 27 28 18 18 29 3 4 25 28 28 28 28 6 7 33 9 15 9 9 9 9 29 66 10 8 33 3 3 10 40 29 18 18 18 18 18 18 19 19 0 3 4 15 16 17 89 89 90 34 33 8 33 3 3 9 9 2 44 66 12 62 62 19 19 0 0 91 45 8 8 8 9 9 0 35 34 0 2 41 41 41 41 18 19 24 4 25 69 51 2 16 17 17 16 1 2 23 16 92 75 29 4 4 25 3 4 3 4 5 5 3 3 9 9 10 69 35 34 36 36 35 35 79 34 33 29 93 93 18 18 10 44 10 29 2 41 26 85 81 3 3 9 9 10 11 32 59 59 36 34 34 10 42 0 37 63 94 0 41 0 33 33 6 33 8 4 18 18 19 19 10 11 47 28 18 18 19 19 6 33 34 2 5 5 3 3 29 71 6 15 8 8 8 8 8 8 29 14 69 88 6 8 8 33 34 2 18 18 18 18 18 18 29 8 33 8 4 41 41 8 8 36 36 29 71 6 80 85 85 81 3 29 29 14 12 9 9 69 52 95 68 96 2 68 61 20 54 41 8 8 9 9 29 3 4 26 50 97 25 14 6 13 98 98 2 68 61 20 20 4 0 29 14 30 69 98 98 9 4 15 9 9 3 3 0 48 44 48 34 8 8 8 8 8 8 9 9 15 33 15 15 36 36 33 33 25 72 29 72 29 18 18 29 3 4 3 4 5 5 3 3 9 9 0 18 24 18 4 18 18 19 19 29 3 4 44 66 6 99 7 7 36 34 44 76 75 76 79 79 34 34 34 36 36 36 36 0 45 10 18 40 6 94 0 41 91 74 74 23 88 23 100 101 101 56 22 20 22 4 45 45 29 14 51 102 56 22 20 22 24 72 72 24 24 25 3 4 25 85 85 15 42 0 37 49 50 26 7 87 7 82 8 8 9 33 69 69 103 16 52 14 12 30 66 66 15 8 33 10 46 46 35 79 79 0 35 34 35 33 9 9 4 0 69 49 82 82 82 29 3 4 26 87 81 3 3 0 48 91 48 0 3 3 9 9 29 3 4 3 4 4 4 18 18 18 18 25 81 67 80 7 8 36 36 0 1 2 33 10 32 32 0 3 4 3 4 4 4 0 3 4 15 9 9 3 3 9 9 15 22 20 22 15 9 9 8 8 9 9 25 3 4 3 4 45 45 10 34 35 34 36 36 33 33 10 11 30 60 98 8 8 8 29 72 29 72 29 18 18 18 18 18 18 0 3 4 15 0 0 35 35 0 3 4 15 8 8 9 9 10 30 46 30 12 12 32 32 10 3 4 15 8 8 36 36 29 8 33 29 12 12 8 8 9 9 0 3 4 3 4 41 41 8 8 9 9 29 91 29 4 5 5 5 18 19 19 29 3 4 25 40 40 29 30 99 66 45 45 18 29 8 33 8 4 41 41 8 8 9 9 15 3 29 25 14 39 39 33 33 15 34 34 34 34 34 8 8 29 3 4 25 67 67 18 18 29 18 24 18 4 4 4 29 45 10 18 40 97 97 25 72 29 88 52 55 21 21 29 18 24 18 4 18 18 19 19 29 3 4 2 91 91 48 10 42 0 3 4 4 4 18 18 18 18 29 3 4 26 50 63 43 104 47 2 41 10 66 14 14 14 10 14 6 13 98 98 6 33 8 4 2 41 0 91 58 18 19 19 4 18 18 18 18 18 18 29 3 4 25 85 85 26 69 105 69 75 89 90 34 33 26 65 26 42 2 91 2 55 20 61 58 58 2 2 48 34 0 41 41 8 8 9 9 10 11 56 20 83 83 2 44 45 10 46 46 79 79 15 38 2 79 83 83 83 2 24 18 4 41 41 8 8 9 9 0 53 73 35 34 36 36 35 35 2 68 61 20 61 61 101 101 2 3 4 25 67 67 18 18 0 3 4 26 50 63 0 91 45 41 41 41 18 19 19 25 13 6 62 40 6 7 33 36 0 48 48 19 19 29 8 33 8 34 8 8 8 8 8 8 36 36 2 29 41 29 12 62 19 19 6 87 6 106 106 29 13 6 62 24 72 72 3 3 29 14 6 13 66 66 6 26 94 27 45 18 25 81 67 81 87 71 71 0 3 4 5 0 38 38 35 35 36 36 2 44 41 8 9 9 15 22 20 22 20 4 4 15 22 20 22 68 30 32 93 53 37 62 40 24 5 25 3 4 25 67 67 18 18 10 40 29 41 41 6 12 31 32 4 41 41 8 8 9 9 15 8 33 34 2 5 5 3 3 6 33 34 2 41 41 8 8 10 40 29 41 41 8 8 9 9 10 11 31 99 8 8 15 22 20 22 20 4 4 29 8 33 8 34 36 36 36 36 8 8 9 9 29 62 40 62 24 13 13 6 14 12 30 30 34 44 1 4 26 27 28 18 18 29 3 4 3 24 19 19 18 18 0 35 34 35 34 34 34 55 55 55 55 29 3 4 3 4 4 4 10 34 2 61 61 96 5 29 30 11 30 66 66 6 41 24 5 25 81 81 29 18 24 0 44 44 8 8 29 72 29 72 29 18 18 18 18 18 18 0 3 4 15 8 8 9 9 29 3 4 15 34 34 8 8 8 8 2 24 18 4 41 41 66 66 26 71 4 15 55 55 2 55 33 8 4 0 0 3 3 6 33 8 4 45 45 4 4 18 18 29 3 4 3 4 18 18 18 18 18 18 29 99 26 31 10 35 36 33 8 4 41 41 4 29 69 88 9 9 3 9 9 2 24 18 4 5 5 3 3 9 9 25 3 4 2 41 41 2 66 66 12 99 29 4 4 18 18 18 18 29 62 40 29 45 45 2 44 91 75 107 59 31 29 18 18 18 18 18 18 29 45 10 58 57 28 28 18 18 29 71 6 3 4 18 18 18 18 18 18 0 3 4 3 4 5 5 3 3 29 8 33 6 30 11 30 12 12 12 12 108 71 3 2 24 0 9 9 8 8 36 36 2 10 45 24 19 19 18 18 10 34 10 30 11 30 8 8 8 8 4 41 41 41 29 4 9 15 4 45 45 18 18 18 18 6 33 29 12 12 8 8 9 9 10 44 39 86 109 5 15 8 33 8 33 3 3 9 9 0 48 39 48 0 2 41 10 73 86 24 24 5 5 15 35 34 35 34 34 34 0 0 91 48 45 45 18 18 6 33 8 4 41 41 8 8 9 9 10 26 110 42 33 33 10 42 0 37 69 99 87 6 6 6 18 18 10 34 35 34 34 34 2 61 68 61 69 43 11 0 91 91 41 41 18 18 29 62 40 29 45 45 6 108 106 15 9 9 15 3 4 2 41 41 4 9 2 68 2 15 15 36 36 36 36 10 42 0 37 50 50 6 7 87 6 62 62 19 19 25 3 4 3 4 4 4 29 3 4 3 24 13 13 15 1 2 3 4 18 18 18 18 18 18 0 3 4 3 33 15 15 34 34 29 62 40 29 41 41 8 8 2 24 18 4 18 18 18 18 18 18 19 19 29 3 4 3 4 18 18 18 18 18 18 19 19 25 3 4 3 4 0 0 3 3 0 3 4 3 4 2 2 44 24 25 72 25 72 3 0 58 86 18 40 28 28 18 18 29 29 10 73 58 18 6 26 99 91 41 8 29 72 29 72 14 111 111 8 8 15 8 33 8 34 8 8 15 22 20 23 103 103 56 22 20 3 4 18 18 19 19 6 33 8 33 9 9 4 4 2 56 2 11 107 47 43 70 25 44 73 45 10 38 42 3 3 9 9 29 14 26 14 29 32 31 29 18 18 18 18 18 18 25 3 4 3 4 41 41 8 8 9 9 29 30 95 52 56 83 79 34 33 8 33 15 15 8 8 0 3 4 3 4 45 45 18 18 18 18 0 3 4 3 4 18 18 18 18 18 18 19 19 0 38 2 35 34 55 55 2 22 4 3 4 45 45 18 18 18 18 10 11 82 82 34 34 33 9 29 3 4 3 4 45 45 29 71 6 3 33 3 3 15 22 20 22 20 2 2 44 44 72 69 110 0 42 0 58 58 6 33 36 0 48 48 44 30 99 12 29 62 62 19 19 10 43 46 43 49 50 26 3 4 15 36 36 10 42 2 51 57 57 44 58 24 18 4 41 41 8 8 9 9 0 18 24 0 1 1 2 44 91 75 89 97 6 33 44 102 72 3 3 29 12 99 12 29 13 13 15 42 0 42 0 18 18 18 18 18 18 19 19 10 43 10 48 48 19 19 29 18 24 18 24 24 24 88 72 29 72 24 88 10 43 43 46 31 32 10 30 46 30 32 36 33 33 29 14 26 14 10 72 29 88 8 8 9 9 29 14 6 13 98 98 10 34 0 2 48 91 48 58 58 19 19 24 19 19 18 18 6 26 104 112 18 18 29 91 44 91 18 18 18 18 29 12 99 12 29 13 13 2 68 61 75 89 97 70 70 29 72 29 72 14 42 42 35 35 36 36 0 0 91 48 19 19 10 26 13 106 98 8 15 3 4 25 67 67 2 41 33 29 72 72 3 3 9 9 0 3 4 2 41 41 18 19 19 0 3 4 15 4 4 8 8 8 8 0 42 0 42 0 41 41 66 66 0 3 4 3 4 4 29 69 60 82 82 85 9 25 3 0 69 49 82 7 7 85 85 6 0 41 41 41 6 99 40 40 0 3 4 6 7 7 8 9 9 0 0 91 45 8 8 8 9 9 0 0 91 48 45 45 18 18 0 35 34 0 109 109 3 3 6 33 8 4';
            $this->lineCycleLength = 2647;
            $this->lineCycleStart = 294;

            $this->rockCycleStart = 197; // 197, 1887, 3577, 5267, 6957, 8647
            $this->rockCycleLength = 1690;

            $this->heightUpToStartOfFirstCycle = 295;
            $this->heightAddedEachCycle = 2647;
        }


        return $this->getFinalAnswer() . "\n";

//        $this->determineCycle();
    }

    public function getFinalAnswer(): int
    {
        $rockTotal = 1000000000000;

//        echo $this->colour("Rock total: $rockTotal\n", 34);

        $rockTotal -= $this->rockCycleStart;

//        echo $this->colour("After removing start: $rockTotal\n\n", 34);

        $cycleCount = floor($rockTotal / $this->rockCycleLength);
        $leftover = $rockTotal % $this->rockCycleLength;

//        echo $this->colour("Full cycles: $cycleCount\n", 33);
//        echo $this->colour("Leftover rocks: $leftover\n\n", 33);

        $cycleHeight = $cycleCount * $this->heightAddedEachCycle;
        $height = $this->heightUpToStartOfFirstCycle + $cycleHeight;

//        echo $this->colour("Height = $this->heightUpToStartOfFirstCycle + $cycleHeight = $height\n\n", 35);

        $goUpTo = $leftover + $this->rockCycleStart;

//        echo $this->colour("Iterating up to $leftover + $this->rockCycleStart = $goUpTo rocks\n", 36);


        $this->chamber = [];
        $this->knownHighest = -1;
        $instructionIndex = 0;

        for ($i = 0; $i <= $goUpTo; $i++) {
            $rock = new Rock($this, $i);

            while (!$rock->stopped) {
                $instruction = $this->getInstruction($instructionIndex);
                $instructionIndex++;

                if ($instruction === '<') {
                    $rock->left();
                } elseif ($instruction === '>') {
                    $rock->right();
                }

                $rock->down();
            }

//            echo "i = $i; height = $this->knownHighest\n";
        }

        $height += ($this->knownHighest - $this->heightUpToStartOfFirstCycle);

        return $height;
    }

    public function determineCycle()
    {
        echo "Starting initial loop\n";
//        for ($j = 3500; $j <= 4000; $j++) {
//            $j = 3580;
            $j = 150;
//            if ($j % 10000 === 0) {
                echo "j = $j\n";
//            }

            $this->chamber = [];
            $this->knownHighest = -1;
            $instructionIndex = 0;

            $cyclesFound = 0;
            $lastChamberCycle = 0;

            $heightAdded = 0;

            for ($i = 0; $i < $j; $i++) {
                if ($i > 0 && $i % 1000 === 0) {
                    echo $i . "\n";
                }

                $rock = new Rock($this, $i);

                while (!$rock->stopped) {
                    $instruction = $this->getInstruction($instructionIndex);
                    $instructionIndex++;

                    if ($instruction === '<') {
                        $rock->left();
                    } elseif ($instruction === '>') {
                        $rock->right();
                    }

                    $rock->down();
                }
                unset($rock);

                if ($i === $this->rockCycleStart - 1) {
                    echo "Height before cycle start: $this->knownHighest\n";
                    $this->heightUpToStartOfFirstCycle = $this->knownHighest;
                }

                if (($i - $this->rockCycleStart) % $this->rockCycleLength === 0) {
                    echo "Rock $i, height $this->knownHighest\n";
                }

//                if (count($this->chamber) > 2f93) {
//                    echo "Rock cycle begins at $i\n";
//                    dump($this->getChambersById());
//                    exit;
//                }

//                if ($cyclesFound < 3) {
//                    if ($this->containsLineCycle()) {
//                        echo "Rock $i contains line cycle (" . count($this->chamber) . " lines)\n";
//                        dump($this->getChambersById());
//                        exit;
//                    }
//                }
            }

//            echo "Detecting cycles\n";
//            $this->detectCycles();
//        }

        echo "Performing checks\n";

        // Window 1 - uses of each row
//        $distinct = $this->getDistinctRows();
//        dump($distinct);
//
//        asort($distinct);
//        dump($distinct);

        // Window 2 - indexed uses of each row
//        $distinct = array_flip(array_keys($this->getDistinctRows()));
//        dump($distinct);


        // Window 3 - detected cycle and chamber output
//        $this->detectCycles();

//        $distinct = array_flip(array_keys($this->getDistinctRows()));
//        $c1 = array_map(fn ($line) => $distinct[$line], $this->getSimpleChamber());
//        dump($this->getChambersById());
//        echo implode(" ", $c1) . "\n";



        // Window 4
//        $this->detectCycles();


//        $this->findCycle(2647);
    }

    public function getChambersById(): array
    {
        $distinct = array_flip(array_keys($this->getDistinctRows()));
        return array_map(fn ($line) => $distinct[$line], $this->getSimpleChamber());
    }

    public function containsLineCycle(): bool
    {
        $chambers = $this->getChambersById();
        $c1s = implode(' ', $chambers);

        $result = strpos($c1s, $this->lineCycle, $this->lastCycleFound);
        if ($result !== false) {
            $this->lastCycleFound = $result + 1;
            return true;
        }

        return false;
    }

    /**
     * Determine if the current chamber ends with the line cycle.
     *
     * Doesn't work properly, because a rock that ends a cycle can also add new
     * lines that are part of the next cycle, therefore str_ends_with might never
     * return true.
     *
     * @return bool
     */
    public function endsWithCycle(): bool
    {
        $chambers = $this->getChambersById();
        $c1s = implode(' ', $chambers);

        return str_ends_with($c1s, $this->lineCycle);
    }

    public function checkLastCycle(): bool
    {
        $length = strlen(preg_replace('/[\S]/', '', $this->lineCycle)) + 1;

        $distinct = array_flip(array_keys($this->getDistinctRows()));
        $c1 = array_map(fn ($line) => $distinct[$line], $this->getSimpleChamber());

        $slice = array_slice($c1, -$length);

        return implode(' ', $slice) === $this->lineCycle;
    }

    public function detectCycles()
    {
        $distinct = array_flip(array_keys($this->getDistinctRows()));

//        dump($distinct);

        $chamber = $this->getSimpleChamber();

        $last100 = [];

        $c1 = array_map(fn ($line) => $distinct[$line], $this->getSimpleChamber());

        $slice = null;

//        echo "Chamber is " . count($chamber) . " big\n";

        for ($i = 2600; $i < count($chamber) / 2; $i++) {

//            if ($i % 5 !== 0) {
//                continue;
//            }

//            if ($i % 1000 === 0) {
//                echo $i . "\n";
//            }

            $c = $c1;
            $slice1 = implode(' ', array_splice($c, -$i));
            $slice2 = implode(' ', array_splice($c, -$i));

            if ($slice1 !== $slice2) {
//                echo "Slice length $i does not match\n";
            } elseif (!$slice || $i % $slice !== 0) {
                echo "Slice length $i MATCHES\n";
                echo $slice1 . "\n";
//                $slice = $i;

                echo "====================================\n";

                echo implode(' ', $c1) . "\n";
            }
        }
    }

    public function findCycle(int $length)
    {
        $distinct = array_flip(array_keys($this->getDistinctRows()));
        $chamber = array_map(fn ($line) => $distinct[$line], $this->getSimpleChamber());

        for ($i = 0; $i <= $length * 10; $i++) {
            $slice1 = implode(' ', array_slice($chamber, $i, $length));
            $slice2 = implode(' ', array_slice($chamber, $length, $length));

            if ($slice1 === $slice2) {
                echo "Found matching slice starting at $i\n";
//                return $i;
            }
        }

        return null;
    }
}
