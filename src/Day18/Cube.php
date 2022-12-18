<?php

namespace App\Day18;

class Cube extends Space
{
    public bool $cube = true;

    public function __toString(): string
    {
        return "■ $this->x,$this->y,$this->z";
    }
}
