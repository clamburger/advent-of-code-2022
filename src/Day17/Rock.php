<?php

namespace App\Day17;

use App\Puzzles\Day17PyroclasticFlow;

class Rock
{
    public const PATTERNS = [
        [
            [1, 1, 1, 1]
        ],
        [
            [0, 1, 0],
            [1, 1, 1],
            [0, 1, 0],
        ],
        [
            [1, 1, 1],
            [0, 0, 1],
            [0, 0, 1],
        ],
        [
            [1],
            [1],
            [1],
            [1],
        ],
        [
            [1, 1],
            [1, 1],
        ]
    ];

    public array $pattern;

    public int $height;

    public int $width;

    public int $x = 2;

    public int $y;

    public bool $stopped = false;

    public function __construct(public Day17PyroclasticFlow $puzzle, public int $id)
    {
        $this->pattern = self::getPattern($id);
        $this->height = count($this->pattern);
        $this->width = count($this->pattern[0]);

        $this->y = $this->puzzle->highestLine() + 4;
    }

    public static function getPattern(int $i): array
    {
        return self::PATTERNS[$i % 5];
    }

    public function on(int $x, int $y): bool
    {
        return !empty($this->pattern[$y - $this->y][$x - $this->x]);
    }

    public function canMoveX(int $moveX, int $moveY = 0): bool
    {
        if ($this->stopped) {
            return false;
        }

        if ($this->x + $moveX < 0) {
            return false;
        }

        if ($this->x + $moveX + $this->width > 7) {
            return false;
        }

        if ($this->y + $moveY < 0) {
            return false;
        }

        foreach ($this->pattern as $y => $row) {
            foreach ($row as $x => $value) {
                if (!$value) {
                    continue;
                }

                if (!empty($this->puzzle->chamber[$y + $this->y + $moveY][$x + $this->x + $moveX])) {
                    return false;
                }
            }
        }

        return true;
    }

    public function left(): void
    {
        if ($this->canMoveX(-1)) {
            $this->x--;
        }
    }

    public function right(): void
    {
        if ($this->canMoveX(1)) {
            $this->x++;
        }
    }

    public function down(): bool
    {
        if ($this->canMoveX(0, -1)) {
            $this->y--;
            return true;
        }

        $this->stop();
        return false;
    }

    public function stop(): void
    {
        foreach ($this->pattern as $y => $row) {
            foreach ($row as $x => $value) {
                if (!$value) {
                    continue;
                }

                $this->puzzle->chamber[$y + $this->y][$x + $this->x] = 1;
                $this->pattern = [];
                $this->stopped = true;

                if ($this->puzzle->knownHighest < $this->y + $this->height - 1) {
                    $this->puzzle->knownHighest = $this->y + $this->height - 1;
                }
            }
        }
    }
}
