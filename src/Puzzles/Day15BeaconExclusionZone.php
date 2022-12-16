<?php

namespace App\Puzzles;

use App\Day15\Sensor;

class Day15BeaconExclusionZone extends AbstractPuzzle
{
    protected static int $day_number = 15;

    /** @var array<Sensor> */
    protected array $sensors;

    protected array $map = [];

    protected array $coveredArea;

    public function parseInput()
    {
        $this->sensors = [];
        $this->coveredArea = [];

        foreach ($this->input->lines as $line) {
            preg_match('/x=([0-9-]+), y=([0-9-]+).*x=([0-9-]+), y=([0-9-]+)/', $line, $matches);
            $sensor = new Sensor($matches[1], $matches[2], $matches[3], $matches[4]);
            $this->sensors[] = $sensor;

            $this->map[$sensor->y][$sensor->x] = 'S';
            $this->map[$sensor->beaconY][$sensor->beaconX] = 'B';
        }
    }
    public function getPartOneAnswer(): int
    {
        $this->parseInput();

        $ranges = [];

        $scount = count($this->sensors) - 1;

        $yCheck = $scount > 20 ? 2000000 : 10;

        foreach ($this->sensors as $index => $sensor) {

//            echo "=== SENSOR $index / $scount ===\n";

            $minY = $sensor->y - $sensor->distance;
            $maxY = $sensor->y + $sensor->distance;

            for ($y = $minY; $y <= $maxY; $y++) {
//                if ($y !== 2000000) {
//                    continue;
//                }
                $diff = abs($sensor->y - $y);
                $reverseDiff = abs($sensor->distance - $diff);

                $ourRange = ['from' => $sensor->x - $reverseDiff, 'to' => $sensor->x + $reverseDiff];

                if ($y === $yCheck) {
                    $ranges[] = $ourRange;
                }
            }
        }

        usort($ranges, function ($a, $b) {
            $s = $a['from'] <=> $b['from'];
            if ($s === 0) {
                return $a['to'] <=> $b['to'];
            }
            return $s;
        });

        $mergedRanges = [array_shift($ranges)];

        while (!empty($ranges)) {
            $ourRange = array_shift($ranges);
            $rangeCovered = false;

            foreach ($mergedRanges as &$range) {
                if ($ourRange['from'] <= $range['to']
                    && $ourRange['to'] >= $range['from']) {
                    $range['from'] = min($range['from'], $ourRange['from']);
                    $range['to'] = max($range['to'], $ourRange['to']);
                    $rangeCovered = true;
                    break;
                }
            }

            if (!$rangeCovered) {
                $mergedRanges[] = $ourRange;
            }
        }

        $count = 0;

        foreach ($mergedRanges as $range) {
            $count += $range['to'] - $range['from'] + 1;
        }

        foreach ($this->map[$yCheck] as $item) {
            $count--;
        }

        return $count;
    }

    public function getPartTwoAnswer(): int
    {
        $this->parseInput();

        $allRanges = [];

        $scount = count($this->sensors) - 1;

        $limit = $scount > 20 ? 4000000 : 20;

        foreach ($this->sensors as $index => $sensor) {
//            echo "=== SENSOR $index / $scount ===\n";

            $minY = min(max(0, $sensor->y - $sensor->distance), $limit);
            $maxY = min(max(0, $sensor->y + $sensor->distance), $limit);

            for ($y = $minY; $y <= $maxY; $y++) {
                $diff = abs($sensor->y - $y);
                $reverseDiff = abs($sensor->distance - $diff);

                if (!isset($ys[$y])) {
                    $ys[$y] = [];
                }

                $ourRange = [
                    'from' => min(max(0, $sensor->x - $reverseDiff), $limit),
                    'to' => min(max(0, $sensor->x + $reverseDiff), $limit),
                ];

                $allRanges[$y][] = $ourRange;
            }
        }

//        echo "=== Sorting ranges... ===\n";

        ksort($allRanges);

//        echo "=== Iterating ranges... ===\n";

//        echo "Ranges to iterate: " . count($allRanges) . "\n";

        foreach ($allRanges as $y => $ranges) {
//            if ($y % 10000 === 0) {
//                echo "y = $y";
//            }

            usort($ranges, function ($a, $b) {
                $s = $a['from'] <=> $b['from'];
                if ($s === 0) {
                    return $a['to'] <=> $b['to'];
                }
                return $s;
            });

//            if ($y % 10000 === 0) {
//                echo "   sorted. " . count($ranges) . " ranges to combine\n";
//            }

            $mergedRanges = [array_shift($ranges)];

            while (!empty($ranges)) {
                $ourRange = array_shift($ranges);
                $rangeCovered = false;

                foreach ($mergedRanges as &$range) {
                    if ($ourRange['from'] <= $range['to'] + 1
                        && $ourRange['to'] >= $range['from'] - 1) {
                        $range['from'] = min($range['from'], $ourRange['from']);
                        $range['to'] = max($range['to'], $ourRange['to']);
                        $rangeCovered = true;
                        break;
                    }
                }

                if (!$rangeCovered) {
                    $mergedRanges[] = $ourRange;
                }
            }

            $allRanges[$y] = $mergedRanges;


            if (count($mergedRanges) > 1) {
//                echo "Found it at y = $y\n";
                $x = $mergedRanges[0]['to'] + 1;
                return $x * 4000000 + $y;
            }
        }

        // something went wrong
        return -1;
    }
}
