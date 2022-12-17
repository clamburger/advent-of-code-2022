<?php

namespace App\Day16;

class Node
{
    public array $tunnels = [];
    public int $flow = 0;
    public array $distanceTo = [];

    public function __construct(public string $id)
    {
    }

    public function __toString()
    {
        return $this->id;
    }
}
