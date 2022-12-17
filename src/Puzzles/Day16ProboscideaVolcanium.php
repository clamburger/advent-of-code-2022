<?php

namespace App\Puzzles;

use App\Day16\Node;
use App\Puzzles;

class Day16ProboscideaVolcanium extends Puzzles\AbstractPuzzle
{
    protected static int $day_number = 16;

    /** @var Node[] */
    public array $nodes;

    /** @var Node[] */
    public array $openings;

    public array $biggestPath;
    public array $paths;

    public int $timeLimit = 30;

    public function parseInput()
    {
        $this->nodes = $this->openings = $this->biggestPath = $this->paths = [];

        foreach ($this->input->lines as $line) {
            preg_match('/Valve (\w+) has flow rate=(\d+); tunnels? leads? to valves? (.+)/', $line, $matches);

            $valve = new Node($matches[1]);
            $this->nodes[$valve->id] = $valve;
        }

        foreach ($this->input->lines as $line) {
            preg_match('/Valve (\w+) has flow rate=(\d+); tunnels? leads? to valves? (.+)/', $line, $matches);

            $valve = $this->nodes[$matches[1]];
            $valve->tunnels = array_map(fn ($id) => $this->nodes[$id], explode(', ', $matches[3]));

            /*
             * To account for the one minute that it takes to open the valve, create an extra node
             * for each valve that has a non-zero flow rate. This node is suffixed with a * and is
             * attached to the valve and its neighbours.
             *
             * No shortest path will ever go through one of these extra nodes; it will only be
             * traversed if the node itself is the destination.
             */
            if ((int)$matches[2] > 0) {
                $node = new Node($matches[1] . '*');
                $node->flow = $matches[2];
                $node->tunnels = $valve->tunnels;

                $valve->tunnels[]          = $node;
                $this->nodes[$node->id]    = $node;
                $this->openings[$node->id] = $node;
            }
        }

        /*
         * https://en.wikipedia.org/wiki/Floyd-Warshall_algorithm (thanks Wikipedia)
         *
         * Calculate the shortest distance between all nodes. Once we have this data, we no longer
         * have to consider any nodes other than the special valve opening nodes.
         */

        foreach ($this->nodes as $valve) {
            foreach ($this->nodes as $valve2) {
                $valve->distanceTo[$valve2->id] = 10000;
            }

            $valve->distanceTo[$valve->id] = 0;
            foreach ($valve->tunnels as $tunnel) {
                $valve->distanceTo[$tunnel->id] = 1;
            }
        }

        foreach ($this->nodes as $k) {
            foreach ($this->nodes as $i) {
                foreach ($this->nodes as $j) {
                    if ($i->distanceTo[$j->id] > $i->distanceTo[$k->id] + $k->distanceTo[$j->id]) {
                        $i->distanceTo[$j->id] = $i->distanceTo[$k->id] + $k->distanceTo[$j->id];
                    }
                }
            }
        }
    }

    public function getPartOneAnswer(): int
    {
        $this->parseInput();
        $this->timeLimit = 30;
        return $this->findBestPath([$this->nodes['AA']], $this->openings, $this->timeLimit);
    }

    /**
     * This is a brute force solver for the travelling salesman problem.
     *
     * Normally, this would take way too long: there are over a trillion permutations of the 15
     * opening nodes. Luckily, the addition of the time limit greatly reduces our search space.
     * On average, 5.7 nodes are visited each time, giving a search space of less than 2 million.
     * That number of permutations can be easily brute forced.
     *
     * @param Node[] $pathSoFar
     * @param Node[] $candidates
     * @param int $timeLeft
     */
    public function findBestPath(array $pathSoFar, array $candidates, int $timeLeft): int
    {
        if (empty($candidates)) {
            return $this->calculatePathScore($pathSoFar);
        }

        $best = -1;

        foreach ($candidates as $candidate) {
            $time = $timeLeft - $pathSoFar[array_key_last($pathSoFar)]->distanceTo[$candidate->id];

            $score = $this->calculatePathScore($pathSoFar);
            if ($score > $best) {
                $best = $score;
//                    echo "$best: " . implode(' ', $pathSoFar) . "\n";
            }

            if ($time <= 0) {
//                $score = $this->calculatePathScore($pathSoFar);
//                if ($score > $best) {
//                    $best = $score;
//                    echo "$best: " . implode(' ', $pathSoFar) . "\n";
//                }
                continue;
            }

            $pathSoFar2 = $pathSoFar;
            $pathSoFar2[] = $candidate;
            $candidates2 = array_values(array_filter($candidates, fn ($n) => $n !== $candidate));

            $score = $this->findBestPath($pathSoFar2, $candidates2, $time);
            if ($score > $best) {
                $best = $score;
//                echo "$best: " . implode(' ', $pathSoFar2) . "\n";
            }
        }

        return $best;
    }

    /**
     * @param Node[] $nodes
     */
    public function calculatePathScore(array $nodes): int
    {
        if (count($nodes) > count($this->biggestPath)) {
            $this->biggestPath = $nodes;
        }

        $score = 0;

        // Sanity check to make sure we haven't constructed an impossibly long path
        $time = $this->timeLimit;

        $current = array_shift($nodes);

        foreach ($nodes as $node) {
            // Subtract travel time
            $time -= $current->distanceTo[$node->id];

            if ($time < 0) {
                // Oops, out of time
                dump(implode(' ', $nodes));
                throw new \Exception('Not enough time');
            }

            // Multiply flow by remaining time to get the score
            $score += $node->flow * $time;

            $current = $node;
        }

        usort($nodes, fn ($a, $b) => $a->id <=> $b->id);
        $this->paths[] = ['nodes' => $nodes, 'score' => $score];

        return $score;
    }

    public function getPartTwoAnswer(): int
    {
        $this->parseInput();
        $this->timeLimit = 26;
        $maxScore = $this->findBestPath([$this->nodes['AA']], $this->openings, $this->timeLimit);

        usort($this->paths, fn ($a, $b) => $b['score'] <=> $a['score']);
        $paths = array_reduce($this->paths, function ($paths, $path) {
            $str = implode(' ', $path['nodes']);
            if (!isset($paths[$str])) {
                $paths[$str] = $path;
            }
            return $paths;
        }, []);

        /*
         * $paths now contains the highest score for each possible combination of nodes.
         * We need to find the two paths that share no nodes and have the highest combined score.
         *
         * This is, once again, just another brute-force. There's about 5000 distinct paths, and
         * even with some culling we have to about a million comparisons. This takes a mildly
         * uncomfortable amount of time.
         *
         * Funnily enough, despite the length of time needed to iterate the whole array, the optimal
         * result for the main input is output almost immediately.
         */

        $bestScore = 0;

//        echo "Max score with one person: $maxScore\n";

        foreach ($paths as $p1) {
            foreach ($paths as $p2) {
                if ($p2['score'] > $p1['score']) {
                    continue;
                }

                $score = $p1['score'] + $p2['score'];
                if ($score < $maxScore) {
                    // If the combined score is lower than the single person score, it means we've
                    // gotten too far down the p2 array to be useful. Move onto the next p1.
                    continue 2;
                }

                if (!empty(array_intersect($p1['nodes'], $p2['nodes']))) {
                    continue;
                }

                $score = $p1['score'] + $p2['score'];
                if ($score > $bestScore) {
//                    echo "$score = " . implode(' ', $p1['nodes']) . " /// " . implode(' ', $p2['nodes']) . "\n";
                    $bestScore = $score;
                }
            }
        }

        return $bestScore;
    }
}
