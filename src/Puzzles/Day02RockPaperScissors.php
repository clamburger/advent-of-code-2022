<?php

namespace App\Puzzles;

class Day02RockPaperScissors extends AbstractPuzzle
{
    protected static int $day_number = 2;

    private const CHOICE_MAP = [
        'A' => 'rock',
        'B' => 'paper',
        'C' => 'scissors',
        'X' => 'rock',
        'Y' => 'paper',
        'Z' => 'scissors',
    ];

    private function parseStrategy(string $line): array
    {
        $letters = explode(' ', $line);

        return [
            'opponent' => self::CHOICE_MAP[$letters[0]],
            'you' => self::CHOICE_MAP[$letters[1]]
        ];
    }

    public function score($you, $opponent): int
    {
        $scores = [
            'rock' => 1,
            'paper' => 2,
            'scissors' => 3,
        ];

        if ($you === $opponent) {
            return $scores[$you] + 3;
        }

        if ($you === 'rock' && $opponent == 'paper'
            || $you === 'paper' && $opponent == 'scissors'
            || $you === 'scissors' && $opponent == 'rock') {
            return $scores[$you];
        }

        return $scores[$you] + 6;
    }

    public function getPartOneAnswer(): int
    {
        $strategy = $this->input->lines->map($this->parseStrategy(...));

        $score = 0;

        foreach ($strategy as $round) {
            $score += $this->score($round['you'], $round['opponent']);
        }

        return $score;
    }

    const RESULT_MAP = [
        'X' => ['rock' => 'scissors', 'scissors' => 'paper', 'paper' => 'rock'],
        'Y' => ['rock' => 'rock', 'scissors' => 'scissors', 'paper' => 'paper'],
        'Z' => ['rock' => 'paper', 'scissors' => 'rock', 'paper' => 'scissors'],
    ];

    private function parseStrategyPartTwo(string $line): array
    {
        $letters = explode(' ', $line);

        $opponent = self::CHOICE_MAP[$letters[0]];
        $you = self::RESULT_MAP[$letters[1]][$opponent];

        return [
            'opponent' => $opponent,
            'you' => $you,
        ];
    }

    public function getPartTwoAnswer(): int
    {
        $strategy = $this->input->lines->map($this->parseStrategyPartTwo(...));

        $score = 0;

        foreach ($strategy as $round) {
            $score += $this->score($round['you'], $round['opponent']);
        }

        return $score;
    }
}
