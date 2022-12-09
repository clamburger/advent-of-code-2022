<?php

namespace App\Day09;

class Rope {
    public int $x = 0;
    public int $y = 0;

    public ?self $parent = null;

    public function applyDir(string $dir): void
    {
        $distance = 1;

        if ($dir === 'R') {
            $this->x += $distance;
        } elseif ($dir === 'L') {
            $this->x -= $distance;
        } elseif ($dir === 'D') {
            $this->y += $distance;
        } elseif ($dir === 'U') {
            $this->y -= $distance;
        }
    }

    public function touching(): bool
    {
        $r = $this->parent;

        if (abs($r->x - $this->x) > 1) {
            return false;
        }

        if (abs($r->y - $this->y) > 1) {
            return false;
        }

        return true;
    }

    public function iterate(): void
    {
        $r = $this->parent;

        if (!$r) {
            return;
        }

        if ($this->touching()) {
            return;
        }

        if ($r->x !== $this->x && $r->y !== $this->y) {
            $this->x += ($r->x > $this->x ? 1 : -1);
            $this->y += ($r->y > $this->y ? 1 : -1);
        } elseif ($r->x !== $this->x) {
            $this->x += ($r->x > $this->x ? 1 : -1);
        } elseif ($r->y !== $this->y) {
            $this->y += ($r->y > $this->y ? 1 : -1);
        }
    }
}
