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

function runPuzzle(AbstractPuzzle $puzzle): void
{
    echo "Day {$puzzle->getDay()}\n";

    $example = $puzzle->withExampleInput();
    if ($example) {
        echo "Part One (example): {$example->getPartOneAnswer()}\n";
    }
    echo "Part One (answer): {$puzzle->getPartOneAnswer()}\n";

    if ($example) {
        echo "Part Two (example): {$example->getPartTwoAnswer()}\n";
    }
    echo "Part Two (answer): {$puzzle->getPartTwoAnswer()}\n";
    echo "\n";
}
