<?php

namespace App;

use Exception;

class Input
{
    /**
     * The contents of the input file, unaltered other than removing any trailing newlines from the file.
     */
    public readonly string $raw;

    /**
     * @var string[] An array of input lines, where each element represents one line.
     *               Trailing whitespace is removed, but leading whitespace is preserved.
     */
    public readonly array $lines;

    public readonly array $grid;

    public readonly array $raw_blocks;

    /**
     * @param string A full path to the input file.
     * @throws Exception Throws an Exception if the input file does not exist.
     */
    public function __construct(string $filepath)
    {
        if (!file_exists($filepath)) {
            throw new Exception(sprintf('File %s does not exist', $filepath));
        }

        $this->raw = rtrim(file_get_contents($filepath), "\r\n");
        $this->lines = array_map('rtrim', explode("\n", $this->raw));
        $this->grid = array_map('str_split', $this->lines);

        $this->raw_blocks = explode("\n\n", $this->raw);
    }
}
