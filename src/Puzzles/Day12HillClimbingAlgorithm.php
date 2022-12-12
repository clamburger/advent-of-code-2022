<?php

namespace App\Puzzles;

use App\Day12\Point;

class Day12HillClimbingAlgorithm extends AbstractPuzzle
{
    protected static int $day_number = 12;

    public array $map;

    public Point $start;
    public Point $end;

    public array $openSet = [];

    protected function parseInput(int $part)
    {
        $this->map = [];

        foreach ($this->input->grid as $row => $cols) {
            foreach ($cols as $col => $value) {
                $point = new Point($this, $col, $row, ord($value) - 96);

                if ($value === 'S') {
                    $point->height = 1;

                    if ($part === 1) {
                        $this->start = $point;
                        $point->distanceFromStart = 0;
                        $point->pathFromStart = [$point];
                    } else {
                        $this->end = $point;
                    }
                } elseif ($value === 'E') {
                    $point->height = 26;
                    if ($part === 1) {
                        $this->end = $point;
                    } else {
                        $this->start = $point;
                        $point->distanceFromStart = 0;
                        $point->pathFromStart = [$point];
                    }
                }

                $this->map[$row][$col] = $point;
            }
        }

        $this->start->recalculateDistanceToEnd();

        $this->openSet = [
            $this->start->id => $this->start
        ];
    }

    public function hypotheticalDistanceToEnd(int $x, int $y): int
    {
        return abs($this->end->x - $x) + abs($this->end->y - $y);
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput(1);

        while (!empty($this->openSet)) {
            $node = array_shift($this->openSet);

            if ($node === $this->end) {
                return $node->distanceFromStart;
            }

            $neighbours = $this->getNeighbours($node, 1);

            foreach ($neighbours as $neighbour) {
                $this->processNeighbour($node, $neighbour);
            }
        }

        // something went wrong
        return -1;
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

    public int $i = 0;

    private function getNeighbours(Point $node, int $part): array
    {
        $x = $node->x;
        $y = $node->y;

        $neighbors = [
            ['x' => $x - 1, 'y' => $y],
            ['x' => $x, 'y' => $y + 1],
            ['x' => $x, 'y' => $y - 1],
            ['x' => $x + 1, 'y' => $y],
        ];

        $neighbors = array_filter($neighbors, function ($pos) use ($node, $part) {
            $neighbor = $this->map[$pos['y']][$pos['x']] ?? null;

            if (!$neighbor) {
                return false;
            }

            if ($part === 1 && $neighbor->height - $node->height > 1) {
                return false;
            }

            if ($part === 2 && $node->height - $neighbor->height > 1) {
                return false;
            }

            return true;
        });

        return array_map(fn ($pos) => $this->map[$pos['y']][$pos['x']], $neighbors);
    }

    public function getPartTwoAnswer(): mixed
    {
        $this->parseInput(2);

        while (!empty($this->openSet)) {
            $node = array_shift($this->openSet);

            $neighbours = $this->getNeighbours($node, 2);

            foreach ($neighbours as $neighbour) {
                $this->processNeighbour($node, $neighbour);
            }
        }

        $lowNodes = [];

        ini_set('memory_limit', -1); // lol

        foreach ($this->map as $row => $cols) {
            foreach ($cols as $col => $node) {
                if ($node->height === 1) {
                    $lowNodes[] = $node;
                }
            }
        }

        uasort($lowNodes, fn ($a, $b) => $a->distanceFromStart <=> $b->distanceFromStart);
        return array_shift($lowNodes)->distanceFromStart;
    }
}
