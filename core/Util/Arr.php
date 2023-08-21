<?php

declare(strict_types=1);

namespace Core\Util;

class Arr
{
    /**
     * @param array<int, mixed> $data
     * @param string $separator
     * @return string
     */
    public static function implodeDeeply(array $data, string $separator = ' '): string
    {
        $data = array_map(function ($value) {
            return \is_array($value) ? self::implodeDeeply($value) : $value;
        }, array_filter($data));

        return implode($separator, $data);
    }
}
