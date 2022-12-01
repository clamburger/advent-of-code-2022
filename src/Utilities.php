<?php

namespace App;

class Utilities
{
    /**
     * Transposes a multidimensional array (switches the rows and columns).
     *
     * @param array $array A two-level array where all rows have the same length.
     * @return array The transposed array.
     */
    public static function transposeArray(array $array): array
    {
        $transposed_array = [];
        $column_count = count($array[0]);

        for ($column = 0; $column < $column_count; $column++) {
            $transposed_array[] = array_column($array, $column);
        }

        return $transposed_array;
    }

    public static function gridToString(array $array): string
    {
        return implode("\n", array_map(fn ($line) => implode('', $line), $array));
    }
}
