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
    public function __construct(bool $example = false)
    {
        if (!static::$day_number) {
            throw new Exception('You need to extend and provide a valid $day_number in ' . get_class($this));
        }

        $filename = sprintf('%s%02d.txt', $example ? 'example' : 'day', static::$day_number);
        $filepath = __DIR__ . '/../../inputs/' . $filename;
        
        $this->input = new Input($filepath);
    }
    
    public function withExampleInput(): ?static
    {
        try {
            return new static(true);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getDay(): int
    {
        return static::$day_number;
    }

    abstract public function getPartOneAnswer(): int|string;
    abstract public function getPartTwoAnswer(): int|string;
}
