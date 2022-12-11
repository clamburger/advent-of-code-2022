<?php

namespace App\Day11;

use App\Puzzles\Day11MonkeyInTheMiddle;
use Brick\Math\BigInteger;
use Brick\Math\RoundingMode;

class Monkey {
    /** @var BigInteger[] */
    public array $items = [];
    public string $operator = '';
    public BigInteger $operatorAmount;
    public BigInteger $divisibleBy;
    public int $ifTrueTemp = 0;
    public int $ifFalseTemp = 0;
    public self $ifTrue;
    public self $ifFalse;

    public int $inspections = 0;

    public array $passChain = [];

    public string $colour;

    public const COLOURS = [31, 32, 33, 34, 35, 36, 37, 97];

    public function __construct(public Day11MonkeyInTheMiddle $puzzle, public int $id, public int $round, public bool $alternate)
    {
        $this->colour = self::COLOURS[$id];
    }

    public function iterate(): void
    {
        $items = $this->items;
        $this->items = [];

//        echo "Monkey $this->id:\n";
        foreach ($items as $worry) {
//            echo "  Inspects item $worry\n";

            $worry1 = $this->operateItem($worry);
            $worry2 = $this->operateItemAlternate($worry);

            if (!$worry1->mod($this->divisibleBy)->isEqualTo($worry2->mod($this->divisibleBy))) {
//                echo "FAILURE:\n";
//                echo "  Standard:  $worry1 % $this->divisibleBy = " . $worry1->mod($this->divisibleBy) . "\n";
//                echo "  Alternate: $worry2 % $this->divisibleBy = " . $worry2->mod($this->divisibleBy) . "\n";
//                exit(1);
            }

            $this->pass($this->alternate ? $worry2 : $worry1);
            $this->inspections++;
        }
    }

    public function operateItem(BigInteger $worry): BigInteger
    {
        $worry = $this->operate($worry);
//            echo "    Item now $worry (operate)\n";

        if ($this->round === 1) {
            $worry = $worry->dividedBy(3, RoundingMode::DOWN);
//                echo "    Item now $worry (divide)\n";
            return $worry;
        }

        return $worry;
    }

    public function operateItemAlternate(BigInteger $worry): BigInteger
    {


        $worry = $this->operate($worry);

//        if ($this->operator === '+') {
        $worry = $worry->mod($this->puzzle->modulus);
//        } elseif ($this->operator === '*') {
//            $worry = $worry->mod($this->operatorAmount->multipliedBy($this->divisibleBy));
//        } else {
//            $worry = $worry->mod($this->operatorAmount->multipliedBy($this->divisibleBy));
//        }

        return $worry;
    }

    public function pass(BigInteger $number): void
    {
        if ($this->check($number)) {
//            echo "    Divisible by $this->divisibleBy\n";
//            echo "    Item with worry level $number passed to monkey {$this->ifTrue->id}.\n";
            $this->ifTrue->items[] = $number;
            $monk = $this->ifTrue;
        } else {
//            echo "    Not divisible by $this->divisibleBy\n";
//            echo "    Item with worry level $number passed to monkey {$this->ifFalse->id}.\n";
            $this->ifFalse->items[] = $number;
            $monk = $this->ifFalse;
        }

        $this->passChain[] = $monk->id;
    }

    public function operate(BigInteger $number): BigInteger
    {
        if ($this->operator === '+') {
            return $number->plus($this->operatorAmount);
        } elseif ($this->operator === '*') {
            Return $number->multipliedBy($this->operatorAmount);
        } elseif ($this->operator === '**') {
            return $number->power($this->operatorAmount->toInt());
        } else {
            throw new \Exception('Oops');
        }
    }

    public function check(BigInteger $number): bool
    {
        return $number->mod($this->divisibleBy)->isZero();
    }
}
