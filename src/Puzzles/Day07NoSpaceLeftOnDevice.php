<?php

namespace App\Puzzles;

use App\Utilities;
use Illuminate\Support\Collection;

class Day07NoSpaceLeftOnDevice extends AbstractPuzzle
{
    protected static int $day_number = 7;

    public function parseInput()
    {
        $dirs = [
            '/' => ['subdirs' => [], 'files' => [], 'subdirsFull' => []]
        ];

        $dirStack = [];
        $cwd = null;

        foreach ($this->input->lines as $line) {
            $line = $line->toString();

            if (empty($line)) {
                continue;
            }

            if ($line[0] === '$') {
                // Command
                $exploded = explode(' ', $line);

                $command = $exploded[1];

                if ($command === 'ls') {
                    continue;
                }

                $argument = $exploded[2];
                if ($argument === '/') {
                    $dirStack = ['/'];
                } elseif ($argument === '..') {
                    array_pop($dirStack);
                } else {
                    $dirStack[] = "$argument/";
                }

                $cwd = implode('', $dirStack);
                if (!isset($dirs[$cwd])) {
                    $dirs[$cwd] = ['subdirs' => [], 'files' => [], 'subdirsFull' => []];
                }
            } else {
                // Output
                $exploded = explode(' ', $line);

                if ($exploded[0] === 'dir') {
                    $dirs[$cwd]['subdirs'][] = $exploded[1];
                    $dirs[$cwd]['subdirsFull'][] = implode('', [...$dirStack, $exploded[1] . '/']);
                } else {
                    $dirs[$cwd]['files'][] = [
                        'name' => $exploded[1],
                        'size' => $exploded[0],
                    ];
                }
            }
        }

        return $dirs;
    }

    public function getPartOneAnswer(): int
    {
        $dirs = $this->parseInput();

        $total = 0;

        foreach ($dirs as $dir) {
            $size = $this->calculateSize($dirs, $dir);
//            echo "Size: " . $size . "\n";

            if ($size <= 100000) {
                $total += $size;
            }
        }

        return $total;
    }

    private function calculateSize(array $dirs, array $dir): int
    {
        $size = 0;

        foreach ($dir['subdirsFull'] as $subdir) {
            $size += $this->calculateSize($dirs, $dirs[$subdir]);
        }

        $size += array_sum(array_column($dir['files'], 'size'));

        return $size;
    }

    public function getPartTwoAnswer(): int
    {
        $dirs = $this->parseInput();

        $usedSpace = $this->calculateSize($dirs, $dirs['/']);
        $freeSpace = 70000000 - $usedSpace;
        $needToFreeAtLeast = 30000000 - $freeSpace;

        $smallestDir = null;

        foreach ($dirs as $name => $dir) {
            $size = $this->calculateSize($dirs, $dir);

            if ($size >= $needToFreeAtLeast) {
                if ($smallestDir === null || $size < $smallestDir) {
//                    echo "Deleting $name would free $size\n";
                    $smallestDir = $size;
                }
            }
        }

        return $smallestDir;
    }
}
