<?php

namespace App\Puzzles;

use App\Utilities;
use Illuminate\Support\Collection;

class Day08TreetopTreeHouse extends AbstractPuzzle
{
    protected static int $day_number = 8;

    private int $height;
    private int $width;

    public function isVisible($x, $y)
    {
        if ($x === 0 || $x === $this->width - 1) {
            return true;
        }

        if ($y === 0 || $y === $this->height - 1)  {
            return true;
        }

        $h = $this->h($x, $y);


        // Left
        $visible = true;
        for ($x2 = 0; $x2 < $x; $x2++) {
            $h2 = $this->h($x2, $y);
            if ($h2 >= $h) {
                $visible = false;
                break;
            }
        }
        if ($visible)  {
            return true;
        }

        // Right
        $visible = true;
        for ($x2 = $this->width - 1; $x2 > $x; $x2--) {
            $h2 = $this->h($x2, $y);
            if ($h2 >= $h) {
                $visible = false;
                break;
            }
        }
        if ($visible)  {
            return true;
        }

        // Up
        $visible = true;
        for ($y2 = 0; $y2 < $y; $y2++) {
            $h2 = $this->h($x, $y2);
            if ($h2 >= $h) {
                $visible = false;
                break;
            }
        }
        if ($visible)  {
            return true;
        }

        // Down
        $visible = true;
        for ($y2 = $this->height - 1; $y2 > $y; $y2--) {
            $h2 = $this->h($x, $y2);
            if ($h2 >= $h) {
                $visible = false;
                break;
            }
        }
        if ($visible)  {
            return true;
        }

        return false;
    }

    public function h($x, $y)
    {
        return (int) $this->input->grid[$y][$x];
    }

    public function processInput()
    {
        $this->height = $this->input->grid->count();
        $this->width = $this->input->grid[0]->count();
    }

    public function getPartOneAnswer(): int
    {
        $this->processInput();

        $trees = $this->input->grid;

        $visible = 0;

        foreach ($trees as $y => $row) {
            foreach ($row as $x => $col) {
                $h = $this->h($x, $y);
                $state = $this->isVisible($x, $y);
                echo "$x, $y ($h): " . ($state ? 'visible' : '') . "\n";
                if ($state) {
                    $visible++;
                }
            }
        }

        return $visible;
    }

    public function getScore($x, $y)
    {
        $scores = [];

        $h = $this->h($x, $y);

        // Left
        if ($x === 0) {
            $scores[] = 0;
        } else {
            $score = 0;
            for ($x2 = $x - 1; $x2 >= 0; $x2--) {
                $score++;
                $h2 = $this->h($x2, $y);
                if ($h2 >= $h) {
                    break;
                }
            }
            $scores[] = $score;
        }

        // Right
        if ($x === $this->width - 1) {
            $scores[] = 0;
        } else {
            $score = 0;
            for ($x2 = $x + 1; $x2 <= $this->width - 1; $x2++) {
                $score++;
                $h2 = $this->h($x2, $y);
                if ($h2 >= $h) {
                    break;
                }
            }
            $scores[] = $score;
        }

        // Up
        if ($y === 0) {
            $scores[] = 0;
        } else {
            $score = 0;
            for ($y2 = $y - 1; $y2 >= 0; $y2--) {
                $score++;
                $h2 = $this->h($x, $y2);
                if ($h2 >= $h) {
                    break;
                }
            }
            $scores[] = $score;
        }

        // Down
        if ($y === $this->height - 1) {
            $scores[] = 0;
        } else {
            $score = 0;
            for ($y2 = $y + 1; $y2 <= $this->height - 1; $y2++) {
                $score++;
                $h2 = $this->h($x, $y2);
                if ($h2 >= $h) {
                    break;
                }
            }
            $scores[] = $score;
        }

        return $scores;
    }

    public function getPartTwoAnswer(): int
    {
        $trees = $this->input->grid;

        $max = 0;

        foreach ($trees as $y => $row) {
            foreach ($row as $x => $col) {
                $scores = $this->getScore($x, $y);
                $total = $scores[0] * $scores[1] * $scores[2] * $scores[3];
                if ($total > $max) {
                    $max = $total;
                }
            }
        }

        return $max;
    }
}
