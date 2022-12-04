<?php

namespace App;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;

class Input
{
    /**
     * The contents of the input file, unaltered other than removing any trailing newlines from the file.
     */
    public readonly Stringable $raw;

    /**
     * @var Collection<Stringable> An array of input lines, where each element represents one line.
     *                             Trailing whitespace is removed, but leading whitespace is preserved.
     */
    public readonly Collection $lines;

    /**
     * @var Collection<Collection<Stringable>>
     */
    public readonly Collection $grid;

    /**
     * @var Collection<Stringable>
     */
    public readonly Collection $raw_blocks;

    /**
     * @var Collection<Collection<Stringable>>
     */
    public readonly Collection $lines_by_block;

    /**
     * @param string $filepath A full path to the input file.
     * @throws Exception Throws an Exception if the input file does not exist.
     */
    public function __construct(string $filepath)
    {
        if (!file_exists($filepath)) {
            throw new Exception(sprintf('File %s does not exist', $filepath));
        }

        Collection::macro('intersectAll', function ($collections) {
            return $collections->reduce(fn ($intersection, $c) => !$intersection ? $c : $intersection->intersect($c));
        });

        $this->raw = str(file_get_contents($filepath))
            ->replace("\r", "") // just windows things
            ->rtrim("\r\n");

        $this->lines = $this->explodeBlock($this->raw);
        $this->grid = $this->lines->map(fn ($line) => collect(str_split($line)));

        $this->raw_blocks = $this->raw
            ->explode("\n\n")
            ->map(str(...));

        $this->lines_by_block = $this->raw_blocks->map($this->explodeBlock(...));
    }

    private function explodeBlock(Stringable $block): Collection
    {
        return $block->explode("\n")->map(fn ($s) => str(rtrim($s)));
    }
}
