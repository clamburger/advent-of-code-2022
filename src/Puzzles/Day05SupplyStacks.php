<?php

namespace App\Puzzles;

use App\Utilities;
use Illuminate\Support\Collection;

class Day05SupplyStacks extends AbstractPuzzle
{
    protected static int $day_number = 5;

    public function parseInput()
    {
        /** @var Collection[] $sections */
        $sections = clone $this->input->lines_by_block;

        /** @var Collection $crates */
        $crates = clone $sections[0];
        $crates->pop();

        $crates = $crates->map(fn ($line) => str_replace(['    ', '[', ']', ' '], ['[*] ', '', '', ''], $line));

        $stacks = [];

        foreach ($crates as $row) {
            $cols = str_split($row);

            foreach ($cols as $index => $col) {
                if ($col === '*') {
                    continue;
                }

                $stacks[$index][] = $col;
            }
        }

        ksort($stacks);

        $instructions = clone $sections[1];

        $parsed = [];

        foreach ($instructions as $instruction) {
            preg_match('/move (\d+) from (\d+) to (\d+)/', $instruction, $matches);

            $parsed[] = [
                'move' => $matches[1],
                'from' => (int)$matches[2] - 1,
                'to' => (int)$matches[3] - 1
            ];
        }

        return [
            'crates' => $stacks,
            'instructions' => $parsed
        ];
    }

    public function getPartOneAnswer(): string
    {
        $input = $this->parseInput();

        $stacks = $input['crates'];

        $instructions = $input['instructions'];

        foreach ($instructions as $instruction) {
            for ($i = 1; $i <= $instruction['move']; $i++) {
                $crate = array_shift($stacks[$instruction['from']]);
                array_unshift($stacks[$instruction['to']], $crate);

//                echo "Moved $crate from {$instruction['from']} to {$instruction['to']}\n";
            }
        }

        $result = '';

        foreach ($stacks as $stack) {
            $result .= $stack[0];
        }

        return $result;
    }

    public function getPartTwoAnswer(): string
    {
        $input = $this->parseInput();

        $stacks = $input['crates'];

        $instructions = $input['instructions'];

        foreach ($instructions as $instruction) {
            $crates = array_splice($stacks[$instruction['from']], 0, $instruction['move']);
            array_splice($stacks[$instruction['to']], 0, 0, $crates);
        }

        $result = '';

        foreach ($stacks as $stack) {
            $result .= $stack[0];
        }

        return $result;
    }
}
