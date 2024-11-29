<?php

namespace Yilanboy\Preview;

class Converter
{
    /**
     * Convert color hex code to RGB
     *
     * @param $hex
     * @return array<int>
     */
    public static function hexToRgb($hex): array
    {
        return sscanf($hex, '#%02x%02x%02x');
    }
}
