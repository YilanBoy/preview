<?php

namespace Yilanboy\Preview\Image;

final class Writer
{
    /**
     * Splits the string into words.
     * This method will split English into words,
     * and Chinese and special symbols into characters.
     *
     * @return array<string>
     */
    public static function splitStringToArray($input): array
    {
        preg_match_all('/\p{Han}|[a-zA-Z0-9]+|\s|[^\p{Han}\s\w]/u', $input, $matches);

        return $matches[0];
    }

    /**
     * Calculate the width of the text image.
     */
    public static function calculateTextImageWidth(
        string $text,
        int $fontSize,
        string $fontPath
    ): int {
        $bbox = imagettfbbox(
            size: $fontSize,
            angle: 0,
            font_filename: $fontPath,
            string: $text
        );

        return $bbox[2] - $bbox[0];
    }

    /**
     * Wrap the text to multiple lines based on the maximum width.
     */
    public function wrapTextImage(
        string $text,
        int $fontSize,
        string $fontPath,
        int $maxWidth
    ): string {
        $wrapText = '';
        $words = $this->splitStringToArray($text);
        $length = count($words);

        for ($i = 0; $i < $length; $i++) {
            $currentWord = $words[$i];
            $proposedText = $wrapText.$currentWord;

            if ($this->calculateTextImageWidth($proposedText, $fontSize, $fontPath) < $maxWidth) {
                $wrapText .= $currentWord;
                continue;
            }

            $wrapText = trim($wrapText);
            $wrapText .= PHP_EOL.$currentWord;
        }

        return $wrapText;
    }
}
