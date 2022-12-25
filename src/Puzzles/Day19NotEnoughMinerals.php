<?php

namespace App\Puzzles;

use App\Day19\Recipe;
use SebastianBergmann\ResourceOperations\ResourceOperations;

class Day19NotEnoughMinerals extends AbstractPuzzle
{
    protected static int $day_number = 19;

    /** @var \App\Day19\Blueprint[] */
    public array $blueprints;

    public const COLOURS = ['ore' => 33, 'clay' => 34, 'obsidian' => 35, 'geode' => 36];

    public const RESOURCES = ['geode', 'obsidian', 'clay', 'ore'];

    public function parseInput()
    {
        $this->blueprints = [];

        foreach ($this->input->lines as $line) {
            $this->blueprints[] = new \App\Day19\Blueprint($this, $line, 24);
        }
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput();

        foreach ($this->blueprints as $blueprint) {
//            echo "Blueprint $blueprint->id: \n";
            $result = $blueprint->calculateMaxGeodes();
//            echo $result . "\n";
        }

        return array_sum(array_map(fn ($b) => $b->quality(), $this->blueprints));
    }

    public function getPartTwoAnswer(): int
    {
        $this->parseInput();

        $this->blueprints = array_slice($this->blueprints, 0, 3);

        foreach ($this->blueprints as $blueprint) {
            $blueprint->maxTime = 32;
//            echo "Blueprint $blueprint->id: \n";
            $result = $blueprint->calculateMaxGeodes();
//            echo $result . "\n";
        }

        return $this->blueprints[0]->maxGeodes * $this->blueprints[1]->maxGeodes * ($this->blueprints[2]?->maxGeodes ?? 1);
    }

    public function colour(string $string, int $colour)
    {
        return "\033[{$colour}m" . $string . "\033[0m";
    }

    public function cr(string $string, string $resource)
    {
        return $this->colour($string, self::COLOURS[$resource]);
    }
}
