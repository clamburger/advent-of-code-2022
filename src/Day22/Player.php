<?php

namespace App\Day22;

use App\Puzzles\Day22MonkeyMap;

class Player
{
    public int $x;
    public int $y = 0;
    public string $facing = 'right';

    public array $track = [];

    public ?int $side = null;
    public ?int $sideX = null;
    public ?int $sideY = null;

    public function __construct(public Day22MonkeyMap $puzzle)
    {
        $firstLine = $puzzle->map->get(1);

        foreach ($firstLine as $index => $item) {
            if ($item === '.') {
                $this->x = $index;
                break;
            }
        }
    }

    public function do(string $instruction): void
    {
//        echo "Instruction: " . $instruction . "\n";

        if ($instruction === 'L' || $instruction === 'R') {
            $this->turn($instruction);

            return;
        }

        $movement = (int)$instruction;

        for ($i = 0; $i < $movement; $i++) {
            if ($this->puzzle->sides === null) {
                $this->moveOne();
            } else {
                $this->moveOneOnCube();
            }
//            $this->puzzle->showMap();
        }
    }

    public function moveOne()
    {
        $x = $this->x;
        $y = $this->y;

        if ($this->facing === 'right') {
            $x += 1;
        } elseif ($this->facing === 'left') {
            $x -= 1;
        } elseif ($this->facing === 'up') {
            $y -= 1;
        } else {
            $y += 1;
        }

        $square = $this->puzzle->map[$y][$x] ?? ' ';
        if ($square === '.') {
            $this->x = $x;
            $this->y = $y;
        } elseif ($square === '#') {
            return;
        } elseif ($square === ' ') {
            if ($this->facing === 'right') {
                $x = $this->puzzle->wrappingLR[$y]['first'];
            } elseif ($this->facing === 'left') {
                $x = $this->puzzle->wrappingLR[$y]['last'];
            } elseif ($this->facing === 'up') {
                $y = $this->puzzle->wrappingTB[$x]['last'];
            } else {
                $y = $this->puzzle->wrappingTB[$x]['first'];
            }

            $newSquare = $this->puzzle->map[$y][$x] ?? ' ';
            if ($newSquare === '.') {
                $this->x = $x;
                $this->y = $y;
            } elseif ($newSquare === '#') {
                return;
            } else {
                throw new \Exception('oops 2');
            }
        } else {
            throw new \Exception('Oops');
        }
    }

    public function moveOneOnCube()
    {
        $sideX = $this->sideX;
        $sideY = $this->sideY;

        if ($this->facing === 'right') {
            $sideX += 1;
        } elseif ($this->facing === 'left') {
            $sideX -= 1;
        } elseif ($this->facing === 'up') {
            $sideY -= 1;
        } else {
            $sideY += 1;
        }

        if (isset($this->puzzle->sides[$this->side][$sideY][$sideX])) {
            // normal move within one side
            $square = $this->puzzle->sides[$this->side][$sideY][$sideX];
            if ($square === '.') {
                $this->sideX = $sideX;
                $this->sideY = $sideY;
                $this->updateGlobalCoords();

                return true;
            } elseif ($square === '#') {
                return false;
            } elseif ($square === ' ') {
                throw new \Exception('oops 3 - this should not be here');
            }
        }

        // We are trying to move to another side
        $newSideDetails = $this->puzzle->edgeMap[$this->side][$this->facing];

        $newSide   = $newSideDetails['side'];
        $newFacing = $newSideDetails['facing'];
        $rotation  = $newSideDetails['rotate'];

        $newCoords = $this->getNewSideCoords($this->sideX, $this->sideY, $this->facing, $rotation);
        $newSideX  = $newCoords['x'];
        $newSideY  = $newCoords['y'];

        $square = $this->puzzle->sides[$newSide][$newSideY][$newSideX];
        if ($square === '.') {
            $this->side   = $newSide;
            $this->sideX  = $newSideX;
            $this->sideY  = $newSideY;
            $this->facing = $newFacing;
            $this->updateGlobalCoords();

            return true;
        } elseif ($square === '#') {
            return false;
        }
    }

    public function getNewSideCoords(int $x, int $y, string $facing, int $rotation): array
    {
        $last = $this->puzzle->sideWidth - 1;

        if ($rotation === 0) {
            if ($facing === 'left') {
                return ['x' => $last, 'y' => $y];
            } elseif ($facing === 'right') {
                return ['x' => 0, 'y' => $y];
            } elseif ($facing === 'up') {
                return ['x' => $x, 'y' => $last];
            } elseif ($facing === 'down') {
                return ['x' => $x, 'y' => 0];
            }
        }

        if ($rotation === 2) {
            if ($facing === 'left') {
                return ['x' => $x, 'y' => $last - $y];
            } elseif ($facing === 'right') {
                return ['x' => $x, 'y' => $last - $y];
            } elseif ($facing === 'up') {
                return ['x' => $last - $x, 'y' => $y];
            } elseif ($facing === 'down') {
                return ['x' => $last - $x, 'y' => $y];
            }
        }

        if ($rotation === 1) {
            if ($facing === 'left') {
                return ['x' => $y, 'y' => 0];
            } elseif ($facing === 'right') {
                return ['x' => $y, 'y' => $last];
            } elseif ($facing === 'up') {
                return ['x' => $last, 'y' => $last - $x];
            } elseif ($facing === 'down') {
                return ['x' => 0, 'y' => $last - $x];
            }
        }

        if ($rotation === 3) {
            if ($facing === 'left') {
                return ['x' => $y, 'y' => $last];
            } elseif ($facing === 'right') {
                return ['x' => $last - $y, 'y' => 0];
            } elseif ($facing === 'up') {
                return ['x' => 0, 'y' => $x];
            } elseif ($facing === 'down') {
                return ['x' => $last, 'y' => $x];
            }
        }

        throw new \Exception("Unhandled case: facing $facing, rotation $rotation");
    }

    public function updateGlobalCoords(): void
    {
        $this->track[$this->y][$this->x] = match ($this->facing) {
            'up' => '↑',
            'down' => '↓',
            'left' => '←',
            'right' => '→',
        };

        $coords  = $this->puzzle->sideCoordsToGlobalCoords($this->side, $this->sideX, $this->sideY);
        $this->x = $coords['x'];
        $this->y = $coords['y'];
    }

    public function turn(string $direction): void
    {
        $turnMap = [
            'left'  => ['L' => 'down', 'R' => 'up'],
            'down'  => ['L' => 'right', 'R' => 'left'],
            'right' => ['L' => 'up', 'R' => 'down'],
            'up'    => ['L' => 'left', 'R' => 'right'],
        ];

        $this->facing = $turnMap[$this->facing][$direction];
    }
}
