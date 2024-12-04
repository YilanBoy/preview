<?php

namespace Yilanboy\Preview;

use GdImage;

class Builder
{
    private const TEXT_MARGIN_RATIO = 0.05;

    public int $width = 1200;

    public int $height = 628;

    public array $title = [
        'text' => 'Hello World!',
        'font_path' => __DIR__.'/../fonts/noto-sans-tc.ttf',
        'font_size' => 50,
        'color' => '#030712',
    ];

    public string $backgroundColor = '#f9fafb';

    public Converter $converter;

    public false|GdImage $image;

    public function __construct()
    {
        $this->converter = new Converter();
    }

    public function size(int $width, int $height): static
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function backgroundColor(string $color): static
    {
        if ($color[0] !== '#') {
            $color = $this->converter->nameToHex($color);
        }

        $this->backgroundColor = $color;

        return $this;
    }

    public function title(
        string $text,
        string $color = 'black',
        int $fontSize = 50,
        string $fontPath = __DIR__.'/../fonts/noto-sans-tc.ttf',
    ): static {
        $this->title['text'] = $text;

        if ($color[0] !== '#') {
            $this->title['color'] = $this->converter->nameToHex($color);
        } else {
            $this->title['color'] = $color;
        }

        $this->title['font_size'] = $fontSize;

        $this->title['font_path'] = $fontPath;

        return $this;
    }

    private function wrapText(
        string $text,
        string $fontSize,
        string $fontPath
    ): string {
        $wrapText = '';
        $length = mb_strlen($text);
        // The maximum width should subtract the width of the border on both sides.
        $maxWidth = round($this->width * (1 - (self::TEXT_MARGIN_RATIO * 2)));

        for ($i = 0; $i < $length; $i++) {
            $currentChar = mb_substr($text, $i, 1, 'UTF-8');
            $proposedText = $wrapText.$currentChar;

            if ($this->calculateTextWidth($proposedText, $fontSize, $fontPath) < $maxWidth) {
                $wrapText .= $currentChar;
                continue;
            }

            $wrapText = trim($wrapText);
            $wrapText .= PHP_EOL.$currentChar;
        }

        return $wrapText;
    }

    private static function calculateTextWidth(
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

    private function configureCanvas(): void
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $backgroundColor = imagecolorallocate(
            $this->image, ...$this->converter->hexToRgb($this->backgroundColor));
        imagefill($this->image, 0, 0, $backgroundColor);
    }

    private function configureTitle(): void
    {
        $wrapTitle = $this->wrapText(
            $this->title['text'], $this->title['font_size'], $this->title['font_path']);

        $titleBbox = imagettfbbox(
            $this->title['font_size'], 0, $this->title['font_path'], $wrapTitle);

        $titleHeight = $titleBbox[1] - $titleBbox[5];

        $x = round($this->width * self::TEXT_MARGIN_RATIO);
        $y = round((imagesy($this->image) - $titleHeight) / 2 - $titleBbox[5]);

        $titleColor = imagecolorallocate(
            $this->image, ...$this->converter->hexToRgb($this->title['color']));

        imagettftext(
            image: $this->image,
            size: $this->title['font_size'],
            angle: 0,
            x: $x,
            y: $y,
            color: $titleColor,
            font_filename: $this->title['font_path'],
            text: $wrapTitle
        );
    }

    public function output(): void
    {
        $this->configureCanvas();
        $this->configureTitle();

        header('Content-Type: image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }

    public function save(string $path): void
    {
        $this->configureCanvas();
        $this->configureTitle();

        imagepng($this->image, $path);
        imagedestroy($this->image);
    }
}
