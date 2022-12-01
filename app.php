<?php

use App\PuzzleRunner;
use App\Puzzles\AbstractPuzzle;

require __DIR__ . '/vendor/autoload.php';

echo "====================================\n";
echo "Welcome to Sam's Advent of Code 2022\n";
echo "====================================\n";
echo "\n";

$puzzle_runner = new PuzzleRunner();
$puzzles = $puzzle_runner->getPuzzles();

if (isset($argv[1])) {
    $day = $argv[1];
    if ($day === 'latest') {
        runPuzzle(array_values($puzzles)[0]);
        exit(0);
    }

    if (!isset($puzzles[$day])) {
        echo "Invalid puzzle specified.\n";
        exit(1);
    }

    runPuzzle($puzzles[$day]);
    exit(0);
}

foreach ($puzzles as $puzzle) {
    runPuzzle($puzzle);
}

function runPuzzle(AbstractPuzzle $puzzle)
{
    echo "Day {$puzzle->getDay()}\n";
    echo "Part One: {$puzzle->getPartOneAnswer()}\n";
    echo "Part Two: {$puzzle->getPartTwoAnswer()}\n";
    echo "\n";
}
