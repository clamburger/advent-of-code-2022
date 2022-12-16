<?php

namespace App\Puzzles;

class Day10CathodeRayTube extends AbstractPuzzle
{
    protected static int $day_number = 10;

    public int $x;

    public array $pending = [];

    public string $output = '';

    public function getPartOneAnswer(): int
    {
        $this->x = 1;

        $lines = clone $this->input->lines;

        $strengths = [];

        $cycle = 0;
        while (true) {
            $cycle++;

            if (($cycle - 20) % 40 === 0 && $cycle <= 220) {
//                echo "$cycle Yes\n";

                $strengths[] = $cycle * $this->x;
            }

            $instruction = (string) $lines->shift();
            if ($instruction && strlen($instruction) > 0) {
                if ($instruction === 'noop') {
                    $this->pending[] = [$instruction, 1];
                } else {
                    $this->pending[] = [$instruction, 2];
                }
            }

            // Handle pending instructions
            if (isset($this->pending[0])) {
                $this->pending[0][1]--;
                if ($this->pending[0][1] === 0) {
                    $detail = array_shift($this->pending);
                    if ($detail[0] === 'noop') {
                    } else {
                        [, $amount] = explode(' ', $detail[0]);
                        $this->x += (int) $amount;
                    }
                }
            }

//            echo "After cycle $cycle, X = $this->x\n";

            if (count($this->pending) === 0 && $lines->isEmpty()) {
                break;
            }
        }

        return array_sum($strengths);
    }

    public function getPartTwoAnswer(): string
    {
        $this->x = 1;

        $lines = clone $this->input->lines;

        $cycle = 0;
        while (true) {
            $cycle++;

            // DURING CYCLE: draw a pixel
            $pixel = ($cycle - 1) % 40;

            if ($this->x === $pixel || $this->x === $pixel + 1 || $this->x === $pixel - 1) {
                $this->output .= '█';
            } else {
                $this->output .= '.';
            }

            if ($cycle % 40 === 0) {
                $this->output .= "\n";
            }

            // DURING CYCLE: add instruction to list with corresponding wait time
            $instruction = (string) $lines->shift();
            if ($instruction && strlen($instruction) > 0) {
                if ($instruction === 'noop') {
                    $this->pending[] = [$instruction, 1];
                } else {
                    $this->pending[] = [$instruction, 2];
                }
            }

            // AFTER CYCLE: decrement wait time for first instruction in pending list, and execute if it's now zero
            if (isset($this->pending[0])) {
                $this->pending[0][1]--;
                if ($this->pending[0][1] === 0) {
                    $detail = array_shift($this->pending);
                    if ($detail[0] === 'noop') {
                    } else {
                        [, $amount] = explode(' ', $detail[0]);
                        $this->x += (int) $amount;
                    }
                }
            }

            if (count($this->pending) === 0 && $lines->isEmpty()) {
                break;
            }
        }

        return "\n" . $this->output;
    }
}
