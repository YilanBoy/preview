<?php

namespace Yilanboy\Preview;

use InvalidArgumentException;

final class ColorConverter
{
    /**
     * Check the string is color hex format
     */
    public function isHexColor(string $color): bool|int
    {
        return preg_match('/^#[a-f0-9]{6}$/i', $color);
    }

    /**
     * Convert color hex code to RGB
     */
    public function hexToRgb(string $hex): array
    {
        if (! $this->isHexColor($hex)) {
            throw new InvalidArgumentException('Invalid hex color');
        }

        return sscanf($hex, '#%02x%02x%02x');
    }

    /**
     * Convert color name to hex code
     */
    public static function nameToHex(string $word): string
    {
        return match (strtolower($word)) {
            'red' => '#ff0000',
            'green' => '#00ff00',
            'blue' => '#0000ff',
            'yellow' => '#ffff00',
            'orange' => '#ffa500',
            'white' => '#ffffff',
            'black' => '#000000',
            default => throw new InvalidArgumentException('Invalid color name'),
        };
    }

}
