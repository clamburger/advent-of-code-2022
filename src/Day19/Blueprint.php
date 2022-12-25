<?php

namespace App\Day19;

use App\Puzzles\Day19NotEnoughMinerals;

/**
 * @property-read Recipe $ore
 * @property-read Recipe $clay
 * @property-read Recipe $obsidian
 * @property-read Recipe $geode
 */
class Blueprint
{
    /** @var Recipe[] */
    public array $recipes = [];

    public array $maxCosts = [];

    public int $maxGeodes;

    public static int $pathCounter = 0;

    public array $paths = [];

    public array $pathsByScore = [];

    public array $openPaths = [];

    public array $maxScoreAtTime = [];

    public int $maxScore;

    public function __construct(public Day19NotEnoughMinerals $puzzle, string $line, public int $maxTime)
    {
        preg_match('/Blueprint (\d+).*costs (.+)\..*costs (.+)\..*costs (.+)\..*costs (.+)\./', $line, $matches);

        $this->id = $matches[1];
        $this->recipes = [
            'ore' => new Recipe('ore', $matches[2]),
            'clay' => new Recipe('clay', $matches[3]),
            'obsidian' => new Recipe('obsidian', $matches[4]),
            'geode' => new Recipe('geode', $matches[5]),
        ];

        $ore = array_map(fn($r) => $r->costs['ore'], $this->recipes);
        $this->maxCosts['ore'] = max(...array_values($ore));
        $this->maxCosts['clay'] = $this->recipes['obsidian']->costs['clay'];
        $this->maxCosts['obsidian'] = $this->recipes['geode']->costs['obsidian'];
    }

    public function __get(string $attribute)
    {
        return array_filter($this->recipes, fn($recipe) => $recipe->type === $attribute)[0] ?? null;
    }

    public function __toString(): string
    {
        return "Blueprint $this->id";
    }

    public function quality(): int
    {
        return $this->id * $this->maxGeodes;
    }

    public function timeToAcquireResourcesForRobot(array $robots, array $resources, Recipe $recipe): int
    {
        $minTime = 0;

        foreach ($recipe->costs as $resource => $amount) {
            $needed = $amount - $resources[$resource];

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

    public function calculateMaxGeodes(): int
    {
        $this->maxScore = 0;

        $resources = array_fill_keys(Day19NotEnoughMinerals::RESOURCES, 0);
        $robots = array_fill_keys(Day19NotEnoughMinerals::RESOURCES, 0);
        $robots['ore'] = 1;

        $result = $this->findBestPath($robots, [], $resources, 0);
        $this->maxGeodes = $result;
        return $result;
    }

    public function findBestPath(array $robots, array $history, array $resources, int $time): int
    {
        self::$pathCounter++;

        $id = self::$pathCounter;

        $this->openPaths[$id] = $history;

        $best = -1;

        foreach (Day19NotEnoughMinerals::RESOURCES as $resource) {
            $recipe = $this->recipes[$resource];
            try {
                $timeToAcquire = $this->timeToAcquireResourcesForRobot($robots, $resources, $recipe);
            } catch (\Exception $e) {
                continue;
            }

            // No point in building something if it will take until minute 23 (or after) to acquire the resources.
            // Even if the resources become available at exactly minute 23, we can't do the construction until
            // minute 24, at which point there's no time available for the robot to mine.
            if ($timeToAcquire + $time >= $this->maxTime - 1) {
                continue;
            }

            if ($resource !== 'geode' && $robots[$resource] >= $this->maxCosts[$resource]) {
                continue;
            }

            // We can't build the robot until *after* the minute when the resources become available.
            // This is because construction orders occur before resource collection.
            $timeToAcquire++;

            $newResources = self::timePasses($timeToAcquire, $robots, $resources);
            $newTime = $time + $timeToAcquire;

            // Subtract the resources for the build and add the robot to our robot list.
            // We don't have to wait a minute for construction since that's accounted for by adding the minute above.
            $newResources = $recipe->build($newResources);
            $newRobots = $robots;
            $newRobots[$resource]++;

            $newHistory = $history;
            $newHistory[$newTime] = $resource;

            $score = $this->score($newRobots, $newHistory, $newResources, $newTime);
            if ($score > $best) {
                $best = $score;
            }

            $benchmark = $this->maxScoreAtTime[$time] ?? 0;
            // If the score is 5 worse than the best known score for this time, assume that we're already
            // too far behind
            if ($benchmark - $score >= 5) {
                continue;
            }


            $score = $this->findBestPath($newRobots, $newHistory, $newResources, $newTime);

            if ($score > $best) {
                $best = $score;
            }
        }

        unset($this->openPaths[$id]);

        return $best;
    }

    public function score(array $robots, array $history, array $resources, int $time)
    {
        $resources = self::timePasses($this->maxTime - $time, $robots, $resources);
        $score = $resources['geode'];

//        $this->paths[] = ['robots' => $robots, 'score' => $score, 'history' => $history];
        $this->paths[] = null;

        $this->maxScore = max($this->maxScore, $score);
        $this->maxScoreAtTime[$time] = max($this->maxScoreAtTime[$time] ?? 0, $score);

//        if (count($this->paths) % 100_000 === 0 && count($this->paths) > 0) {
//            echo "  "
//                . $this->puzzle->colour(date('[H:i:s] '), 37)
//                . number_format(count($this->paths))
//                . ' '
//                . $this->puzzle->colour(str_pad("$score / {$this->maxScore}", 8, ' ', STR_PAD_LEFT), 32)
//                . '   '
//                . $this->puzzle->colour(str_pad("$score / {$this->maxScoreAtTime[$time]} @ $time", 12, ' ' , STR_PAD_LEFT), 34)
//                . '   '
//                . $this->puzzle->colour($this->formatHistory($history), 37)
//                . "\n";
//        }

//        $this->pathsByScore[$score][] = ['robots' => $robots, 'score' => $score, 'history' => $history];
        return $score;
    }

    public function formatHistory(array $history): string
    {
        $array = [];
        foreach ($history as $time => $resource) {
            $array[] = "$time: $resource";
        }
        return implode(', ', $array);
    }

    public static function timePasses(int $minutes, array $robots, array $resources): array
    {
        foreach (Day19NotEnoughMinerals::RESOURCES as $resource) {
            $resources[$resource] += $robots[$resource] * $minutes;
        }
        return $resources;
    }

}
