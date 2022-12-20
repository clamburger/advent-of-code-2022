<?php

namespace App\Puzzles;

use App\Input;
use Exception;

abstract class AbstractPuzzle
{
    /**
     * @var Input The input for the puzzle.
     */
    protected Input $input;

    protected static int $day_number = 0;

    /**
     * @throws Exception Throws an exception if the $day_number is invalid or if the input doesn't exist
     */
    public function __construct()
    {
        if (!static::$day_number) {
            throw new Exception('You need to extend and provide a valid $day_number in ' . get_class($this));
        }
    }

    public static function createFromFile(string $filepath): static
    {
        if (!file_exists($filepath)) {
            throw new Exception(sprintf('File %s does not exist', $filepath));
        }

        $puzzle = new static;
        $contents = file_get_contents($filepath);
        $puzzle->input = new Input($contents);

        return $puzzle;
    }

    public static function createFromInput(): static
    {
        $filename = sprintf('%s%02d.txt', 'day', static::$day_number);
        $filepath = __DIR__ . '/../../inputs/' . $filename;
        return self::createFromFile($filepath);
    }

    public static function createFromExample(): static
    {
        $filename = sprintf('%s%02d.txt', 'example', static::$day_number);
        $filepath = __DIR__ . '/../../inputs/' . $filename;
        return self::createFromFile($filepath);
    }

    public static function createFromString(string $input): static
    {
        $puzzle = new static;
        $puzzle->input = new Input($input);
        return $puzzle;
    }

    public function getDay(): int
    {
        return static::$day_number;
    }

    abstract public function getPartOneAnswer(): int|string;
    abstract public function getPartTwoAnswer(): int|string;
}
