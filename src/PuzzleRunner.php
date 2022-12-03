<?php

namespace App;

use App\Puzzles\AbstractPuzzle;
use App\Puzzles\Day01CalorieCounting;
use App\Puzzles\Day02Dive;
use App\Puzzles\Day03BinaryDiagnostic;
use App\Puzzles\Day04GiantSquid;
use App\Puzzles\Day05HydrothermalVenture;
use App\Puzzles\Day06Lanternfish;

class PuzzleRunner
{
    /** @var AbstractPuzzle[] */
    private array $puzzles = [];

    public function __construct()
    {
        $puzzle_dir = __DIR__ . '/Puzzles';
        $puzzle_files = glob($puzzle_dir . '/Day*.php');

        foreach ($puzzle_files as $file) {
            $class_name = basename($file, '.php');
            $full_class_name = 'App\\Puzzles\\' . $class_name;
            $puzzle = new $full_class_name;
            $this->puzzles[$puzzle->getDay()] = $puzzle;
        }

        // Reverse the puzzle list so that the newest puzzles are at the start
        $this->puzzles = array_reverse($this->puzzles, true);
    }

    public function getPuzzles(): array
    {
        return $this->puzzles;
    }
}
