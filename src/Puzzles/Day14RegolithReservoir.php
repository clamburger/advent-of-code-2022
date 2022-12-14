<?php

namespace App\Puzzles;

class Day14RegolithReservoir extends AbstractPuzzle
{
    protected static int $day_number = 14;

    protected array $map = [];

    protected int $floor = 0;

    protected int $xmin = 100000;
    protected int $xmax = 0;

    public function parseInput()
    {
        $this->map = [];
        $this->floor = 0;

        foreach ($this->input->lines as $line) {
            $instructions = explode(' -> ', $line);

            $x1 = $y1 = null;

            foreach ($instructions as $instruction) {
                [$x2, $y2] = explode(',', $instruction);

                if ($x1 === null) {
                    $this->map[$y2][$x2] = 'rock';
                    $x1 = $x2;
                    $y1 = $y2;
                    continue;
                }

                if ($y1 === $y2) {
                    $xmin = min($x1, $x2);
                    $xmax = max($x1, $x2);
                    for ($x = $xmin; $x <= $xmax; $x++) {
                        $this->map[$y1][$x] = 'rock';
                    }
                }

                if ($x1 === $x2) {
                    $ymin = min($y1, $y2);
                    $ymax = max($y1, $y2);
                    for ($y = $ymin; $y <= $ymax; $y++) {
                        $this->map[$y][$x1] = 'rock';
                    }
                }

                $x1 = $x2;
                $y1 = $y2;
                
                if ($x2 < $this->xmin) {
                    $this->xmin = $x2;
                } elseif ($x2 > $this->xmax) {
                    $this->xmax = $x2;
                }

                if ($y2 > $this->floor) {
                    $this->floor = $y2;
                }
            }
        }

        $this->floor += 2;
    }

    private function colour(string $string, int $colour)
    {
        return "\033[{$colour}m" . $string . "\033[0m";
    }

    public function drawMap(?int $xsand = null, ?int $ysand = null)
    {
        for ($y = 0; $y <= $this->floor; $y++) {
            for ($x = $this->xmin; $x <= $this->xmax; $x++) {
                $item = $this->map[$y][$x] ?? null;

                if ($x === $xsand && $y === $ysand) {
                    echo $this->colour("S", 33);
                } elseif ($item === 'rock') {
                    echo $this->colour("#", 37);
                } elseif ($item === 'sand') {
                    echo $this->colour("o", 33);
                } else {
                    echo ' ';
                }
            }
            echo "\n";
        }
        echo "\n";
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput();
//        $this->drawMap();

        $sands = 0;

        while (true) {
            $x = 500;
            $y = 0;

            $stopped = false;
            while (!$stopped) {
                $result = $this->moveDown($x, $y);
//                $this->drawMap(494, 503, 0, 9, $result['x'], $result['y']);
                $x = $result['x'];
                $y = $result['y'];
                $stopped = $result['stopped'];

                if ($stopped) {
                    $sands++;
                    $this->map[$y][$x] = 'sand';
                }

                if ($y > 10000) {
//                    $this->drawMap();
                    break 2;
                }
            }
        }

        return $sands;
    }

    public function moveDown(int $x, int $y): array
    {
        $candidates = [
            ['x' => $x, 'y' => $y + 1], // Down
            ['x' => $x - 1, 'y' => $y + 1], // Down left
            ['x' => $x + 1, 'y' => $y + 1], // Down right
        ];

        foreach ($candidates as $candidate) {
            if (!$this->blocked($candidate['x'], $candidate['y'])) {
                return ['x' => $candidate['x'], 'y' => $candidate['y'], 'stopped' => false];
            }
        }

        return ['x' => $x, 'y' => $y, 'stopped' => true];
    }

    public function blocked(int $x, int $y): bool
    {
        return isset($this->map[$y][$x]);
    }

    public function addFloor()
    {
        for ($x = 0; $x <= 1000; $x++) {
            $this->map[$this->floor][$x] = 'rock';
        }
    }

    public function getPartTwoAnswer(): mixed
    {
        $this->parseInput();
        $this->addFloor();

        $sands = 0;

        while (true) {
            $x = 500;
            $y = 0;

            $stopped = false;
            while (!$stopped) {
                $result = $this->moveDown($x, $y);
//                $this->drawMap($result['x'], $result['y']);
                $x = $result['x'];
                $y = $result['y'];
                $stopped = $result['stopped'];

                if ($stopped) {
                    $sands++;
                    $this->map[$y][$x] = 'sand';

                    if ($x === 500 && $y === 0) {
                        break 2;
                    }
                }

                if ($y > 10000) {
                    throw new \Exception('error');
                }
            }
        }

        $this->xmin -= 150;
        $this->xmax += 150;
//        $this->drawMap();

        return $sands;
    }
}
