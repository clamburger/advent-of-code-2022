<?php

namespace App\Day21;

use App\Puzzles\Day21MonkeyMath;

class Monkey
{
    public ?string $operator = null;
    public string $colour;
    public ?float $number = null;
    public ?string $monkey1Name = null;
    public ?string $monkey2Name = null;
    public ?self $monkey1 = null;
    public ?self $monkey2 = null;

    public bool $human = false;

    public const COLOURS = [31, 32, 33, 34, 35, 36, 37, 97];

    public function __construct(public Day21MonkeyMath $puzzle, public string $id, string $instruction)
    {
        $parts = explode(' ', $instruction);
        if (count($parts) === 1) {
            $this->number = $parts[0];
        } else {
            $this->monkey1Name = $parts[0];
            $this->operator = $parts[1];
            $this->monkey2Name = $parts[2];
        }
    }

    public function tryToApplyNumber(): void
    {
        if ($this->human || $this->number !== null) {
            return;
        }

        if ($this->monkey1->number !== null && $this->monkey2->number !== null) {
            $this->number = match ($this->operator) {
                '+' => $this->monkey1->number + $this->monkey2->number,
                '-' => $this->monkey1->number - $this->monkey2->number,
                '*' => $this->monkey1->number * $this->monkey2->number,
                '/' => $this->monkey1->number / $this->monkey2->number,
                '=' => null,
            };
        }
    }

    public function __toString(): string
    {
        return $this->id . ' ' . $this->number;
    }
}
