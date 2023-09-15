<?php

declare(strict_types=1);

namespace Core\Util;

class Str
{
    public static function snake(string $value): string
    {
        $pattern = '/([a-z])([A-Z])/';

        $replacement = '$1_$2';

        return strtolower(preg_replace($pattern, $replacement, $value));
    }
}
