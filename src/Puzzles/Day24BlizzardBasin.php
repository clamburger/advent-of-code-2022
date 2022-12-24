<?php

namespace App\Puzzles;

use App\Day24\Blizzard;
use App\Day24\Point;

class Day24BlizzardBasin  extends AbstractPuzzle
{
    protected static int $day_number = 24;

    /** @var array<Blizzard> */
    public array $blizzards;

    public array $map;

    public array $mapOverTime;

    public int $height;
    public int $width;

    public array $start = ['x' => 1, 'y' => 0];

    public array $end;

    /** @var Point[] */
    public array $openSet = [];

    public \DateTimeInterface $timer;

    public int $t;

    public function parseInput()
    {
        $this->resetMap();

        /** @var Point $startPoint */
        $startPoint = $this->map[$this->start['y']][$this->start['x']];
        $startPoint->distanceFromStart = 0;
        $startPoint->pathFromStart = [$startPoint];
        $startPoint->recalculateDistanceToEnd();

        $this->openSet = [
            $startPoint->id => $startPoint
        ];

        $this->mapOverTime = [];
        $this->mapOverTime[] = $this->map;

        // 700 = the lowest common multiple of 35 and 100 (the area of the input field)
        for ($i = 1; $i < 1400; $i++) {
            if ($i % 100 === 0) {
                echo $i . "\n";
            }

            $this->map = $this->cloneMapLayer($this->map, $i);
            $this->mapOverTime[] = $this->map;

            foreach ($this->blizzards as $blizzard) {
                $blizzard->move();
            }
        }

        $this->t = 0;
    }

    public function resetMap()
    {
        $this->map = $this->blizzards = [];

        foreach ($this->input->grid as $y => $row) {
            foreach ($row as $x => $char) {
                if ($char === '#') {
                    $this->map[$y][$x] = '#';
                } elseif ($char === '.') {
                    $this->map[$y][$x] = new Point($this, $x, $y, 0);
                } else {
                    $point = new Point($this, $x, $y, 0);
                    $this->map[$y][$x] = $point;

                    $blizzard = new Blizzard($this, $x, $y, $char);
                    $this->blizzards[] = $blizzard;
                    $point->blizzards[$blizzard->id] = $blizzard;
                }
            }
        }

        $this->height = count($this->map);
        $this->width = count($this->map[0]);

        $this->end = ['x' => $this->width - 2, 'y' => $this->height - 1];
    }

    public function getMapString(?array $map = null, ?int $playerX = null, ?int $playerY = null): string
    {
        if ($map === null) {
            $map = $this->map;
        }
        $str = "";
        foreach ($map as $y => $row) {
            foreach ($row as $x => $square) {
                if ($square === '#') {
                    $str .= $this->colour('█', 37);
                } elseif (count($square->blizzards) === 0) {
                    if ($playerX === $x && $playerY === $y) {
                        $str .= $this->colour('E', 93);
                    } else {
                        $str .= ' ';
                    }
                } elseif (count($square->blizzards) === 1) {
                    $str .= $this->colour(reset($square->blizzards)->direction, 36);
                } else {
                    $str .= $this->colour(count($square->blizzards), 96);
                }
            }
            $str .= "\n";
        }
        return $str;
    }

    public function showMap(?array $map = null, ?int $playerX = null, ?int $playerY = null): void
    {
        echo $this->getMapString($map, $playerX, $playerY);
    }

    private function colour(string $string, int $colour, ?string $colour2 = null): string
    {
        if ($colour2 === null) {
            return "\033[{$colour}m" . $string . "\033[0m";
        }

        return "\033[{$colour}m\033[{$colour2}m" . $string . "\033[0m";
    }

    public function processNeighbour(Point $current, Point $neighbour)
    {
        $score = $current->distanceFromStart + 1;

        if ($neighbour->distanceFromStart > $score) {
            $neighbour->pathFromStart = $current->pathFromStart;
            $neighbour->pathFromStart[] = $neighbour;
            $neighbour->distanceFromStart = $score;
            $neighbour->recalculateDistanceToEnd();

            $this->addToOpenSet($neighbour);
        }
    }

    public function addToOpenSet(Point $node)
    {
        if (isset($this->openSet[$node->id])) {
            return;
        }

        $this->openSet[$node->id] = $node;
        uasort($this->openSet, fn (Point $a, Point $b) => $a->maybeDistanceToEnd <=> $b->maybeDistanceToEnd);
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput();
        $this->timer = new \DateTimeImmutable();

//        $this->showMap(null, 1, 0);

        return $this->getShortestDistance()->distanceFromStart;
    }

    public function getShortestDistance(): ?Point
    {
        $i = 0;

        while (!empty($this->openSet)) {
            $i++;

            $point = array_shift($this->openSet);

//            if ($i % 5000 === 0 || $point->isEnd()) {
//                $timeDiff = (new \DateTime())->diff($this->timer);
//
//                echo "Iteration $i\n";
//                echo "  [" . $timeDiff->format("%H:%I:%S") . "]\n";
//                echo "  Open set: " . count($this->openSet) . "\n";
//                $best = $this->openSet[array_key_first($this->openSet)];
//                echo "  Best: " . $best->id . "\n";
//                echo "    Maybe to end: " . $best->maybeDistanceToEnd . "\n";
//                echo "    From start: " . $best->distanceFromStart . "\n";
//
//                $worst = $this->openSet[array_key_last($this->openSet)];
//                echo "  Worst: " . $worst->id . "\n";
//                echo "    Maybe to end: " . $worst->maybeDistanceToEnd . "\n";
//                echo "    From start: " . $worst->distanceFromStart . "\n";
//                echo "\n";
//            }

            if ($point->isEnd()) {
                $path = $point->pathFromStart;

//                foreach ($path as $t => $pathPoint) {
//                    echo "Minute $t\n";
//                    $this->showMap($this->mapOverTime[$t], $pathPoint->x, $pathPoint->y);
//                }

                return $point;
            }

            $neighbours = $point->getCandidates();

            foreach ($neighbours as $neighbour) {
                $this->processNeighbour($point, $neighbour);
            }
        }
    }

    public function cloneMapLayer(array $map, int $t): array
    {
        $newMap = [];
        foreach ($map as $y => $row) {
            foreach ($row as $x => $square) {
                if (is_object($square)) {
                    $point = clone $square;
                    $point->updateT($t);
                    $newMap[$y][$x] = $point;
                } else {
                    $newMap[$y][$x] = $square;
                }
            }
        }

        return $newMap;
    }

    public function getPartTwoAnswer(): int
    {
        $this->parseInput();
        $this->timer = new \DateTimeImmutable();

//        $this->showMap(null, 1, 0);

        $endPoint = $this->getShortestDistance();

//        echo "Trip 1: " . $endPoint->distanceFromStart . "\n";

        // Reinitialize for the return trip
        $this->start = $this->end;
        $this->end = ['x' => 1, 'y' => 0];

        $startPoint = $endPoint;
        $startPoint->distanceFromStart = 0;
        $startPoint->pathFromStart = [$startPoint];
        $startPoint->recalculateDistanceToEnd();

        $this->openSet = [
            $startPoint->id => $startPoint
        ];


        $endPoint = $this->getShortestDistance();

//        echo "Trip 2: " . $endPoint->distanceFromStart . "\n";

        // Reinitialize for the return return trip
        $this->end = $this->start;
        $this->start = ['x' => 1, 'y' => 0];

        $startPoint = $endPoint;
        $startPoint->distanceFromStart = 0;
        $startPoint->pathFromStart = [$startPoint];
        $startPoint->recalculateDistanceToEnd();

        $this->openSet = [
            $startPoint->id => $startPoint
        ];

        $endPoint = $this->getShortestDistance();

//        echo "Trip 3: " . $endPoint->distanceFromStart . "\n";

        return $endPoint->t;
    }
}
