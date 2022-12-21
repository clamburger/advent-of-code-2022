<?php

namespace App\Puzzles;

use App\Day21\Monkey;

class Day21MonkeyMath extends AbstractPuzzle
{
    protected static int $day_number = 21;

    /** @var Monkey[] */
    public array $monkeys;

    public function parseInput()
    {
        $this->monkeys = [];

        foreach ($this->input->lines as $line) {
            $parts = explode(': ', $line);

            $monkey = new Monkey($this, $parts[0], $parts[1]);
            $this->monkeys[$monkey->id] = $monkey;
        }

        foreach ($this->monkeys as $monkey) {
            if ($monkey->monkey1Name) {
                $monkey->monkey1 = $this->monkeys[$monkey->monkey1Name];
                $monkey->monkey2 = $this->monkeys[$monkey->monkey2Name];
            }
        }
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput();

        do {
            $unknownMonkeys = array_filter($this->monkeys, fn ($monkey) => $monkey->number === null);

            foreach ($unknownMonkeys as $monkey) {
                $monkey->tryToApplyNumber();
            }
        } while (!empty($unknownMonkeys));

        return (int) $this->monkeys['root']->number;
    }

    public function getPartTwoAnswer(): int
    {
        $this->setUpMonkeysForPartTwo();

        // First, get all monkeys we still don't know about
        $unknownMonkeys = $this->iterateAllUnknownMonkeys();

        $root = $this->monkeys['root'];
        echo "Desired number: " . number_format($root->monkey2->number) . "\n";

//        $dependencyChain = [];

        // human-based iteration (lol)
        $i = 3093175982594;

        while (true) {
            $this->setUpMonkeysForPartTwo();
            $this->monkeys['humn']->number = $i;
            $this->iterateAllUnknownMonkeys();

            $root = $this->monkeys['root'];
            echo "$i = " . number_format($root->monkey1->number);

            if ($root->monkey1->number > $root->monkey2->number) {
                echo " (too big)";
            } elseif ($root->monkey1->number < $root->monkey2->number) {
                echo " (too small)";
            } else {
                echo " (just right)";
            }
            echo "\n";

            if ($root->monkey1->number == $root->monkey2->number) {
                return $i;
            }

            $i++;
        }

        return -1;
    }

    public function setUpMonkeysForPartTwo()
    {
        $this->parseInput();

        $this->monkeys['root']->operator = '=';
        $this->monkeys['humn']->number = null;
        $this->monkeys['humn']->human = true;
    }

    public function iterateAllUnknownMonkeys(): array
    {
        $previousIteration = -1;

        do {
            $unknownMonkeys = array_filter($this->monkeys, fn ($monkey) => $monkey->number === null);
            if ($previousIteration === count($unknownMonkeys)) {
                break;
            }

            foreach ($unknownMonkeys as $monkey) {
                $monkey->tryToApplyNumber();
            }

            $previousIteration = count($unknownMonkeys);
        } while (!empty($unknownMonkeys));

        return $unknownMonkeys;
    }
}
