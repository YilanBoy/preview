<?php

namespace Yilanboy\Preview\Images;

final class TextHandler
{
    public static function splitStringToArray($input): array
    {
        preg_match_all('/\p{Han}|[a-zA-Z0-9]+|\s|[^\p{Han}\s\w]/u', $input, $matches);

        return $matches[0];
    }

    public static function calculateTextImageWidth(
        string $text,
        string $fontSize,
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
