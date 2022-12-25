<?php

namespace App\Day19;

class Recipe
{
    /** @var array<string, int> */
    public array $costs = ['ore' => 0, 'clay' => 0, 'obsidian' => 0, 'geode' => 0];

    public function __construct(public string $type, string $match)
    {
        $costs = explode(' and ', $match);

        foreach ($costs as $cost) {
            $cost = explode(' ', $cost);
            $this->costs[$cost[1]] = (int)$cost[0];
        }
    }

    public function build(array $resources): array
    {
        foreach ($this->costs as $resource => $amount) {
            $resources[$resource] -= $amount;
        }
        return $resources;
    }

    public function __toString(): string
    {
        return "$this->type robot";
    }
}
