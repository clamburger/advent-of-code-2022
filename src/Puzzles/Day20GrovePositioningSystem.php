<?php

namespace App\Puzzles;

class Day20GrovePositioningSystem extends AbstractPuzzle
{
    protected static int $day_number = 20;

    public array $numbers;
    public array $original;
    public int $currentIteration;

    public function parseInput(int $part = 1)
    {
        $count = -1;
        $this->numbers = $this->input->lines->map(function ($n) use (&$count, $part) {
            $count++;
            return ($part === 1 ? $n : (string)$n * 811589153) . '--' . $count;
        })->toArray();
        $this->original = $this->numbers;
        $this->currentIteration = 0;
    }

    public function iterate()
    {
        $fullNumber = $this->original[$this->currentIteration];
        $number = (int)(explode('--', $fullNumber)[0]);

        if ($number === 0) {
            $this->currentIteration++;
            return isset($this->original[$this->currentIteration]);
        }

        $index = array_search($fullNumber, $this->numbers);
        $neighborIndex = $index - 1;
        if ($neighborIndex === -1) {
            $neighborIndex = count($this->numbers) - 1;
        }
        $neighbor = $this->numbers[$neighborIndex];

        array_splice($this->numbers, $index, 1);

//        echo "Want to move " . ($number) . " after left neighbor {$neighbor}\n";

        $neighborIndex = array_search($neighbor, $this->numbers);

        $newIndex = $neighborIndex + $number;


        if ($newIndex < 0) {
            $newIndex += (count($this->numbers) * 10000000000);
        }

        if ($newIndex >= count($this->numbers)) {
            $newIndex = $newIndex % count($this->numbers);
        }

//        echo "New left neighbor at index {$newIndex} is {$this->numbers[$newIndex]}\n";

        array_splice($this->numbers, $newIndex + 1, 0, [$fullNumber]);

//        echo $this->colour("  " . implode(', ', $this->numbers) . "\n", 32);

        $this->currentIteration++;
        return isset($this->original[$this->currentIteration]);
    }

    public function mix()
    {
        $this->currentIteration = 0;
//        echo $this->colour("  " . implode(', ', $this->numbers) . "\n", 32);

        foreach ($this->original as $j => $number) {
//            echo "$j. $number\n";

            $this->iterate();
        }
    }

    public function getResult(): int
    {
        $i = -1;

        $answers = [];

        $afterZero = null;

        $combinedNumbers = [];
        while (count($combinedNumbers) < count($this->numbers) + 4000) {
            $combinedNumbers = [...$combinedNumbers, ...$this->numbers];
        }

        while (true) {
            $i++;
            if ($afterZero !== null) {
                $afterZero++;
            }

            $index = $i % count($combinedNumbers);
            $num = (int)explode('--', $combinedNumbers[$index])[0];

            if ($num === 0 && $afterZero === null) {
                $afterZero = 0;
            }

            if ($afterZero === 1000) {
                $answers[] = $num;
            } elseif ($afterZero === 2000) {
                $answers[] = $num;
            } elseif ($afterZero === 3000) {
                $answers[] = $num;
                break;
            }
        }

        return $answers[0] + $answers[1] + $answers[2];
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput(1);

        $this->mix();

        return $this->getResult();
    }

    private function colour(string $string, int $colour): string
    {
        return "\033[{$colour}m" . $string . "\033[0m";
    }

    public function getPartTwoAnswer(): int
    {
        $this->parseInput(2);
        for ($i = 0; $i < 10; $i++) {
            $this->mix();
        }
        return $this->getResult();
    }
}
