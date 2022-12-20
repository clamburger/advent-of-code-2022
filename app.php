<?php

use App\PuzzleRunner;
use App\Puzzles\AbstractPuzzle;

require __DIR__ . '/vendor/autoload.php';

echo "====================================\n";
echo "Welcome to Sam's Advent of Code 2022\n";
echo "====================================\n";
echo "\n";

ini_set('memory_limit', -1);

$puzzle_runner = new PuzzleRunner();
$puzzles = $puzzle_runner->getPuzzles();

if (isset($argv[1])) {
    $day = $argv[1];
    if ($day === 'latest') {
        runPuzzle(array_values($puzzles)[0], $argv);
        exit(0);
    }

    if (!isset($puzzles[$day])) {
        echo "Invalid puzzle specified.\n";
        exit(1);
    }

    runPuzzle($puzzles[$day], $argv);
    exit(0);
}

foreach ($puzzles as $puzzle) {
    runPuzzle($puzzle, $argv);
}

function runPuzzle(AbstractPuzzle $puzzle, array $args): void
{
    echo "Day {$puzzle->getDay()}\n";

    $example = $puzzle::createFromExample();
    if (!in_array('--part-two-only', $args)) {
        if (!in_array('--input-only', $args)) {
            echo "Part One (example): {$example->getPartOneAnswer()}\n";
        }
        if (!in_array('--example-only', $args)) {
            echo "Part One (answer): {$puzzle->getPartOneAnswer()}\n";
        }
    }

    if (!in_array('--part-one-only', $args)) {
        if (!in_array('--input-only', $args)) {
            echo "Part Two (example): {$example->getPartTwoAnswer()}\n";
        }
        if (!in_array('--example-only', $args)) {
            echo "Part Two (answer): {$puzzle->getPartTwoAnswer()}\n";
        }
    }

    echo "\n";
}
