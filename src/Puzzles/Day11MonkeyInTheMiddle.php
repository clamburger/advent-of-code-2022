<?php

namespace App\Puzzles;

use App\Day11\Monkey;
use Brick\Math\BigInteger;
use Illuminate\Support\Stringable;

class Day11MonkeyInTheMiddle extends AbstractPuzzle
{
    protected static int $day_number = 11;

    /** @var array<int, Monkey>  */
    protected array $monkeys = [];

    /** @var array<int, Monkey>  */
    protected array $monkeysAlt = [];

    public BigInteger $modulus;

    public function parseInput(int $part)
    {
        $this->monkeys = [];
        $this->monkeysAlt = [];

        foreach ($this->input->lines_by_block as $index => $block) {
            $monkey = new Monkey($this, $index, $part, false);

            /** @var Stringable $line */
            foreach ($block as $line) {
                $line = $line->trim();

                if ($line->startsWith('Monkey')) {
                    continue;
                }

                if ($line->startsWith('Starting items:')) {
                    $temp = $line->explode(': ')[1];
                    $monkey->items = array_map(fn ($i) => BigInteger::of($i), explode(', ', $temp));
                    continue;
                }

                if ($line->startsWith('Operation:')) {
                    $temp = trim($line->explode('new = old ')[1]);

                    $parts = explode(' ', $temp);

                    if ($parts[1] === 'old') {
                        $monkey->operator = '**';
                        $monkey->operatorAmount = BigInteger::of(2);
                    } else {
                        $monkey->operator = $parts[0];
                        $monkey->operatorAmount = BigInteger::of($parts[1]);
                    }
                    continue;
                }

                if ($line->startsWith('Test')) {
                    $monkey->divisibleBy = BigInteger::of($line->explode(' by ')[1]);
                    continue;
                }

                if ($line->startsWith('If true')) {
                    $monkey->ifTrueTemp = (int)$line->explode('monkey ')[1];
                    continue;
                }

                if ($line->startsWith('If false')) {
                    $monkey->ifFalseTemp = (int)$line->explode('monkey ')[1];
                    continue;
                }
            }

            $this->monkeys[] = $monkey;

            $monkeyAlt = clone $monkey;
            $monkeyAlt->alternate = true;

            $this->monkeysAlt[] = $monkeyAlt;
        }

        foreach ($this->monkeys as $monkey) {
            $monkey->ifTrue = $this->monkeys[$monkey->ifTrueTemp];
            $monkey->ifFalse = $this->monkeys[$monkey->ifFalseTemp];
        }

        foreach ($this->monkeysAlt as $monkey) {
            $monkey->ifTrue = $this->monkeysAlt[$monkey->ifTrueTemp];
            $monkey->ifFalse = $this->monkeysAlt[$monkey->ifFalseTemp];
        }

        $this->modulus = array_reduce($this->monkeys, function (?BigInteger $carry, Monkey $item) {
            return $carry ? $carry->multipliedBy($item->divisibleBy) : $item->divisibleBy;
        });
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput(1);

        $round = 1;

        while ($round <= 20) {
            foreach ($this->monkeys as $monkey) {
                $monkey->iterate();
            }

//            echo "===================\n";
//            echo "AFTER ROUND $round\n";

            foreach ($this->monkeys as $monkey) {
//                echo "Monkey $monkey->id: " . implode(', ', $monkey->items) . "\n";
            }

//            echo "===================\n";

            $round++;
        }

        $monkeys = $this->monkeys;
        usort($monkeys, fn ($a, $b) => $b->inspections <=> $a->inspections);

        return $monkeys[0]->inspections * $monkeys[1]->inspections;
    }

    public function getPartTwoAnswer(): int
    {
        $this->monkeys = [];

        $this->parseInput(2);

        $round = 1;

        while ($round <= 10000) {
//            foreach ($this->monkeys as $monkey) {
//                $monkey->iterate($round);
//            }

            foreach ($this->monkeysAlt as $monkey) {
                $monkey->iterate();
            }

//            echo $round . "\n";

//            if (in_array($round, [1, 20, 1000, 2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000])) {
//                echo "===================\n";
//                echo "AFTER ROUND $round\n";
//
//                foreach ($this->monkeys as $i => $monkey) {
//                    $altMonkey = $this->monkeysAlt[$i];
//                    echo $this->colour("Monkey $monkey->id:", $monkey->colour) . "\n";
//                    echo "  Correct: " . implode(", ", array_map(fn ($s) => $this->colour($s, Monkey::COLOURS[$s]), $monkey->passChain)) . "\n";
//                    echo "  Alt:     " . implode(", ", array_map(fn ($s) => $this->colour($s, Monkey::COLOURS[$s]), $altMonkey->passChain)) . "\n";
//
//                    echo "  Correct: " . $monkey->inspections . " inspections\n";
//                    echo "  Alt:     " . $altMonkey->inspections . " inspections\n";
//                    echo "\n";
//                }
//            }

            $round++;

//            if ($round === 21) {
//                break;
//            }
        }

        $monkeys = $this->monkeysAlt;
        usort($monkeys, fn ($a, $b) => $b->inspections <=> $a->inspections);

        return $monkeys[0]->inspections * $monkeys[1]->inspections;
    }

    private function colour(string $string, int $colour)
    {
        return "\033[{$colour}m" . $string . "\033[0m";
    }
}
