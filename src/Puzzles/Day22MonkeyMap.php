<?php

namespace App\Puzzles;

use App\Day22\Player;
use Illuminate\Support\Collection;

class Day22MonkeyMap extends AbstractPuzzle
{
    protected static int $day_number = 22;

    /** @var Collection<Collection<string>> */
    public Collection $map;

    public array $instructions;

    public Player $player;

    public array $wrappingLR;
    public array $wrappingTB;

    public array $edgeMap;

    public int $width;

    public int $height;

    public int $sideWidth;

    public ?Collection $sides = null;

    public array $sideStartCoords;

    public function parseInput()
    {
        /** @var Collection $map */
        $map = $this->input->lines_by_block->first();

        $this->width = max(...$map->map(fn ($line) => strlen($line)));

        if ($this->width === 150) {
            $this->sideWidth = 50;
        } else {
            $this->sideWidth = 4;
        }
        $this->height = $map->count();

        /** @var Collection $map */
        $map = $map->map(fn ($line) => str_pad($line, $this->width))
            ->map(fn ($line) => str_split($line));

//        $map->prepend(array_fill(0, $width + 2, ' '));
//        $map->push(array_fill(0, $width + 2, ' '));

        $this->map = $map->map(fn ($line) => collect($line));
        $this->wrappingLR = $this->wrappingTB = [];

        foreach ($this->map as $y => $line) {
            $first = $line->search(fn ($square) => $square !== ' ');
            $last = $line->reverse()->search(fn ($square) => $square !== ' ');

            if ($first !== null) {
                $this->wrappingLR[$y] = ['first' => $first, 'last' => $last];
            }
        }

        for ($x = 0; $x < $this->map->first()->count(); $x++) {
            $first = null;
            $last = null;

            for ($y = 0; $y < $this->map->count(); $y++) {
                $square = $this->map[$y][$x];
                if ($first === null && $square !== ' ') {
                    $first = $y;
                }

                if ($first !== null && $last === null && $square === ' ') {
                    $last = $y - 1;
                }
            }

            if ($first !== null) {
                $this->wrappingTB[$x] = ['first' => $first, 'last' => $last ?? $this->map->count() - 1];
            }
        }

        $instructions = $this->input->lines_by_block->get(1)->first();
        $this->instructions = preg_split('/([LR])/', $instructions, -1, PREG_SPLIT_DELIM_CAPTURE);

        $this->player = new Player($this);
    }

    public function parseInputPartTwo()
    {
        $this->parseInput();

        if ($this->width === 150) {
            $this->sideWidth = 50;
            $this->edgeMap = [
                1 => [
                    'up'    => ['side' => 6, 'facing' => 'right', 'rotate' => 3], // A
                    'left'  => ['side' => 4, 'facing' => 'right', 'rotate' => 2], // G
                    'right' => ['side' => 2, 'facing' => 'right', 'rotate' => 0], // E
                    'down'  => ['side' => 3, 'facing' => 'down',  'rotate' => 0], // B
                ],
                2 => [
                    'up'    => ['side' => 6, 'facing' => 'up',    'rotate' => 0], // H
                    'left'  => ['side' => 1, 'facing' => 'left',  'rotate' => 0], // E
                    'right' => ['side' => 5, 'facing' => 'left',  'rotate' => 2], // I
                    'down'  => ['side' => 3, 'facing' => 'left',  'rotate' => 3], // J
                ],
                3 => [
                    'up'    => ['side' => 1, 'facing' => 'up',    'rotate' => 0], // B
                    'left'  => ['side' => 4, 'facing' => 'down',  'rotate' => 1], // K
                    'right' => ['side' => 2, 'facing' => 'up',    'rotate' => 1], // J
                    'down'  => ['side' => 5, 'facing' => 'down',  'rotate' => 0], // C
                ],
                4 => [
                    'up'    => ['side' => 3, 'facing' => 'right', 'rotate' => 3], // K
                    'left'  => ['side' => 1, 'facing' => 'right', 'rotate' => 2], // G
                    'right' => ['side' => 5, 'facing' => 'right', 'rotate' => 0], // F
                    'down'  => ['side' => 6, 'facing' => 'down',  'rotate' => 0], // D
                ],
                5 => [
                    'up'    => ['side' => 3, 'facing' => 'up',    'rotate' => 0], // C
                    'left'  => ['side' => 4, 'facing' => 'left',  'rotate' => 0], // F
                    'right' => ['side' => 2, 'facing' => 'left',  'rotate' => 2], // I
                    'down'  => ['side' => 6, 'facing' => 'left',  'rotate' => 3], // L
                ],
                6 => [
                    'up'    => ['side' => 4, 'facing' => 'up',    'rotate' => 0], // D
                    'left'  => ['side' => 1, 'facing' => 'down',  'rotate' => 1], // A
                    'right' => ['side' => 5, 'facing' => 'up',    'rotate' => 1], // L
                    'down'  => ['side' => 2, 'facing' => 'down',  'rotate' => 0], // H
                ]
            ];
        } else {
            // rotate:
            // from the perspective of the side you're leaving,
            // how many times has the new side been rotated clockwise?
            // arrow pointing up = 0
            //                right = 1
            //                down = 2
            //                left = 3
            $this->sideWidth = 4;
            $this->edgeMap = [
                1 => [
                    'up'    => ['side' => 2, 'facing' => 'down',  'rotate' => 2], // A
                    'left'  => ['side' => 3, 'facing' => 'down',  'rotate' => 1], // E
                    'right' => ['side' => 6, 'facing' => 'up',    'rotate' => 2], // N
                    'down'  => ['side' => 4, 'facing' => 'down',  'rotate' => 0], // B
                ],
                2 => [
                    'up'    => ['side' => 1, 'facing' => 'down',  'rotate' => 2], // A
                    'left'  => ['side' => 6, 'facing' => 'up',    'rotate' => 3], // M
                    'right' => ['side' => 3, 'facing' => 'right', 'rotate' => 0], // I
                    'down'  => ['side' => 5, 'facing' => 'up',    'rotate' => 2], // D
                ],
                3 => [
                    'up'    => ['side' => 1, 'facing' => 'right', 'rotate' => 3], // E
                    'left'  => ['side' => 2, 'facing' => 'left',  'rotate' => 0], // I
                    'right' => ['side' => 4, 'facing' => 'right', 'rotate' => 0], // H
                    'down'  => ['side' => 5, 'facing' => 'right', 'rotate' => 1], // F
                ],
                4 => [
                    'up'    => ['side' => 1, 'facing' => 'up',    'rotate' => 0], // B
                    'left'  => ['side' => 3, 'facing' => 'left',  'rotate' => 0], // H
                    'right' => ['side' => 6, 'facing' => 'down',  'rotate' => 3], // K
                    'down'  => ['side' => 5, 'facing' => 'down',  'rotate' => 0], // C
                ],
                5 => [
                    'up'    => ['side' => 4, 'facing' => 'up',    'rotate' => 0], // C
                    'left'  => ['side' => 3, 'facing' => 'up',    'rotate' => 3], // F
                    'right' => ['side' => 6, 'facing' => 'right', 'rotate' => 0], // G
                    'down'  => ['side' => 2, 'facing' => 'up',    'rotate' => 2], // D
                ],
                6 => [
                    'up'    => ['side' => 4, 'facing' => 'left',  'rotate' => 1], // K
                    'left'  => ['side' => 5, 'facing' => 'left',  'rotate' => 0], // G
                    'right' => ['side' => 1, 'facing' => 'left',  'rotate' => 2], // N
                    'down'  => ['side' => 2, 'facing' => 'right', 'rotate' => 1], // M
                ]
            ];
        }

        $sideStartCoords = [];

        $side = 1;

        $lastStartX = $lastStartY = null;

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $square = $this->map[$y][$x];

                if ($square === ' ') {
                    continue;
                }

                if ($lastStartX === null
                    || $x - $lastStartX === $this->sideWidth
                    || $y - $lastStartY === $this->sideWidth) {
                    $lastStartX = $x;
                    $lastStartY = $y;

                    $sideStartCoords[$side] = ['x' => $x, 'y' => $y];
                    $side++;
                }
            }
        }

        $this->sides = collect();
        foreach ($sideStartCoords as $sideNumber => $coords) {
            $side = collect();

            for ($y = $coords['y']; $y < $coords['y'] + $this->sideWidth; $y++) {
                $row = collect();
                for ($x = $coords['x']; $x < $coords['x'] + $this->sideWidth; $x++) {
                    $row->push($this->map[$y][$x]);
                }
                $side->push($row);
            }

            $this->sides[$sideNumber] = $side;
        }

        $this->sideStartCoords = $sideStartCoords;
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput();

        foreach ($this->instructions as $instruction) {
            $this->player->do($instruction);
        }

        $row = $this->player->y;
        $col = $this->player->x;

        $facing = ['right' => 0, 'down' => 1, 'left' => 2, 'up' => 3][$this->player->facing];

        return 1000 * ($row + 1) + 4 * ($col + 1) + $facing;
    }

    public function getPartTwoAnswer(): int
    {
        $this->parseInputPartTwo();

        $this->player->side = 1;
        $this->player->sideX = 0;
        $this->player->sideY = 0;

//        $this->showMap();

        foreach ($this->instructions as $instruction) {
            $this->player->do($instruction);
        }

//        $this->showMap();

        $row = $this->player->y;
        $col = $this->player->x;

        $facing = ['right' => 0, 'down' => 1, 'left' => 2, 'up' => 3][$this->player->facing];

        return 1000 * ($row + 1) + 4 * ($col + 1) + $facing;
    }

    private function colour(string $string, int $colour, ?string $colour2 = null)
    {
        if ($colour2 === null) {
            return "\033[{$colour}m" . $string . "\033[0m";
        }

        return "\033[{$colour}m\033[{$colour2}m" . $string . "\033[0m";
    }

    public function showMap()
    {
        for ($y = 0; $y < $this->map->count(); $y++) {
            if ($y % $this->sideWidth === 0 && $this->sides !== null) {
                echo "\n";
            }
            for ($x = 0; $x < $this->map->first()->count(); $x++) {
                $square = $this->map[$y][$x];
                if ($x % $this->sideWidth === 0 && $this->sides !== null) {
                    echo " ";
                }
                if ($x === $this->player->x && $y === $this->player->y) {
                    if ($square === '#') {
                        throw new \Exception('oops - on a brick');
                    }
                    echo $this->colour("P", 47, '1;30');
                } elseif ($square === '#') {
                    echo $this->colour("#", 31);
                } elseif ($square === '.') {
                    if (isset($this->player->track[$y][$x])) {
                        echo $this->colour($this->player->track[$y][$x], 47, 93);
                    } else {
                        echo $this->colour(" ", 47);
                    }
                } else {
                    echo " ";
                }
            }
            echo "\n";
        }


        echo str_repeat("=", $this->width + 5) . "\n";
    }

    public function sideCoordsToGlobalCoords(int $side, int $sideX, int $sideY): array
    {
        return [
            'x' => $this->sideStartCoords[$side]['x'] + $sideX,
            'y' => $this->sideStartCoords[$side]['y'] + $sideY,
        ];
    }
}
