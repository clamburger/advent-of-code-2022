<?php

namespace App\Puzzles;

use SebastianBergmann\ResourceOperations\ResourceOperations;

class Day19NotEnoughMinerals extends AbstractPuzzle
{
    protected static int $day_number = 19;

    /** @var Blueprint[] */
    public array $blueprints;

    public array $robots;

    public array $resources;

    public const COLOURS = ['ore' => 33, 'clay' => 34, 'obsidian' => 35, 'geode' => 36];

    public const RESOURCES = ['geode', 'obsidian', 'clay', 'ore'];

    public function parseInput()
    {
        $this->blueprints = [];

        $this->resources = array_fill_keys(self::RESOURCES, 0);
        $this->robots = array_fill_keys(self::RESOURCES, 0);
        $this->robots['ore'] = 1;

        foreach ($this->input->lines as $line) {
            $this->blueprints[] = new Blueprint($line);
        }
    }

    public static function getResourcesMined(array $robots): array
    {
        return $robots;
    }

    public function arrayHasRobotType(array $robots, string $type)
    {
        return !empty($robots[$type]);
    }

    public function timeToAcquireResourcesForRobot(array $robots, array $resources, Recipe $recipe): int
    {
        $minTime = 0;

        foreach ($recipe->costs as $cost) {
            $resource = $cost->type;
            $needed = $cost->amount - $resources[$resource];

            if ($needed <= 0) {
                continue;
            }

            if ($robots[$resource] === 0) {
                throw new \Exception("No $resource robots available");
            }

            $minTime = max($minTime, ceil($needed / $robots[$resource]));
        }

        return $minTime;
    }

    public array $paths = [];

    public function findBestPath(array $robots, array $history, array $resources, int $time, Blueprint $blueprint): int
    {
        $canBuild = false;

        $best = -1;

        $score = $this->score($robots, $history, $resources, $time, $blueprint);
        if ($score > $best) {
            $best = $score;
        }

        foreach (self::RESOURCES as $resource) {
            $recipe = $blueprint->recipes[$resource];
            try {
                $timeToAcquire = $this->timeToAcquireResourcesForRobot($robots, $resources, $recipe);
            } catch (\Exception $e) {
                continue;
            }

            if ($timeToAcquire + $time > 24) {
                continue;
            }

            $canBuild = true;
            $newResources = $this->timePasses($timeToAcquire, $robots, $resources);
            $time += $timeToAcquire;
            $newResources = $recipe->build($newResources);

            $newRobots = $robots;
            $newRobots[$resource]++;

            $history[$time] = $resource;

//            print_r($history);

            $score = $this->findBestPath($newRobots, $history, $newResources, $time, $blueprint);

            if ($score > $best) {
                $best = $score;
            }
        }

        return $best;
    }

    public function score(array $robots, array $history, array $resources, int $time, Blueprint $blueprint)
    {
        $resources = $this->timePasses(24 - $time, $robots, $resources);
        $score = $resources['geode'];

        $this->paths[] = ['robots' => $robots, 'score' => $score, 'history' => $history];
        return $score;
    }

    public function timePasses(int $minutes, array $robots, array $resources): array
    {
        foreach (self::RESOURCES as $resource) {
            $resources[$resource] += $robots[$resource] * $minutes;
        }
        return $resources;
    }


    public function getPartOneAnswer(): int
    {
        $this->parseInput();

        $score = 0;

        foreach ($this->blueprints as $blueprint) {
            $this->findBestPath($this->robots, [], $this->resources, 1, $blueprint);
            exit;

            for ($m = 1; $m <= 24; $m++) {
                echo $this->colour("== Minute $m ==\n", 37);

                // PHASE 1: build robots, subtract resources
                $newRobots = [];

                foreach (['geode', 'obsidian', 'clay', 'ore'] as $resource) {
                    $recipe = $blueprint->recipes[$resource];
                    while ($recipe->canBuild($this->resources)) {
                        $recipe->build($this->resources);

                        echo sprintf(
                            "Start building a %s\n",
                            $this->cr("$resource robot", $resource),
                        );

                        $newRobots[] = new Robot($resource);
                    }
                }

                // PHASE 2: collect resources
                $mined = self::getResourcesMined($this->robots);

                foreach ($mined as $resource => $amount) {
                    if ($amount === 0) {
                        continue;
                    }
                    $this->resources[$resource] += $amount;
                    echo sprintf(
                        "%s collects %s; you now have %s\n",
                        $this->cr("$amount $resource robot", $resource),
                        $this->cr("$amount $resource", $resource),
                        $this->cr("{$this->resources[$resource]} $resource", $resource)
                    );
                }

                // PHASE 3: new robots are ready
                foreach ($newRobots as $robot) {
                    $this->robots[] = $robot;
                    echo sprintf(
                        "The new %s is ready; you now have %s\n",
                        $this->cr("$robot->type robot", $robot->type),
                        $this->cr(self::getResourcesMined($this->robots)[$robot->type] . " of them", $robot->type)
                    );
                }

                echo "\n";
            }

            break;
            $score += $blueprint->quality();
        }

        return $score;
    }

    public function getPartTwoAnswer(): int
    {
        return 0;
    }

    private function colour(string $string, int $colour)
    {
        return "\033[{$colour}m" . $string . "\033[0m";
    }

    private function cr(string $string, string $resource)
    {
        return $this->colour($string, self::COLOURS[$resource]);
    }
}

/**
 * @property-read Recipe $ore
 * @property-read Recipe $clay
 * @property-read Recipe $obsidian
 * @property-read Recipe $geode
 */
class Blueprint {
    /** @var Recipe[] */
    public array $recipes = [];

    public function __construct(string $line)
    {
        preg_match('/Blueprint (\d+).*costs (.+)\..*costs (.+)\..*costs (.+)\..*costs (.+)\./', $line, $matches);

        $this->id = $matches[1];
        $this->recipes = [
            'ore' => new Recipe('ore', $matches[2]),
            'clay' => new Recipe('clay', $matches[3]),
            'obsidian' => new Recipe('obsidian', $matches[4]),
            'geode' => new Recipe('geode', $matches[5]),
        ];
    }

    public function __get(string $attribute)
    {
        return array_filter($this->recipes, fn ($recipe) => $recipe->type === $attribute)[0] ?? null;
    }

    public function __toString(): string
    {
        return "Blueprint $this->id";
    }

    public function quality(): int
    {
        return $this->id * $this->maxGeodes();
    }

    public function maxGeodes(): int
    {
        // todo: fill this in
        return 0;
    }
}

class Robot {
    public function __construct(public string $type)
    {
    }

    public function __toString()
    {
        return "$this->type robot";
    }
}

class Recipe {
    /** @var Cost[] */
    public array $costs = [];

    public function __construct(public string $type, string $match)
    {
        $costs = explode(' and ', $match);

        foreach ($costs as $cost) {
            $cost = explode(' ', $cost);
            $this->costs[] = new Cost($cost[1], $cost[0]);
        }
    }

    public function canBuild(array $resources): bool
    {
        foreach ($this->costs as $cost) {
            if ($resources[$cost->type] < $cost->amount) {
                return false;
            }
        }
        return true;
    }

    public function build(array $resources): array
    {
        foreach ($this->costs as $cost) {
            $resources[$cost->type] -= $cost->amount;
        }
        return $resources;
    }

    public function __toString(): string
    {
        return "$this->type robot";
    }
}

class Cost {
    public function __construct(public string $type, public int $amount)
    {
    }

    public function __toString(): string
    {
        return "$this->amount $this->type";
    }
}
