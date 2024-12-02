<?php

namespace Yilanboy\Preview;

use GdImage;

class Generator
{
    private const TEXT_MARGIN_RATIO = 0.05;

    public int $width = 1200;

    public int $height = 628;

    public int $fontSize = 50;

    public string $fontPath;

    public string $title = 'Hello World!';

    public string $imageBackgroundColor = '#f9fafb';

    public string $titleColor = '#030712';

    public Converter $converter;

    public false|GdImage $image;

    public function __construct()
    {
        $this->converter = new Converter();
        $this->fontPath = dirname(__FILE__).'/../fonts/noto-sans-tc.ttf';
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

        $this->imageBackgroundColor = $color;

        return $this;
    }

    public function titleColor(string $color): static
    {
        if ($color[0] !== '#') {
            $color = $this->converter->nameToHex($color);
        }

        $this->titleColor = $color;

        return $this;
    }

    public function fontPath(string $fontPath): static
    {
        $this->fontPath = $fontPath;

        return $this;
    }

    public function fontSize(int $fontSize): static
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    private function wrapTitle(string $title): string
    {
        $wrapTitle = '';
        $length = mb_strlen($title);
        // The maximum width should be minus both sides
        $maxWidth = round($this->width * (1 - (self::TEXT_MARGIN_RATIO * 2)));

        for ($i = 0; $i < $length; $i++) {
            $currentChar = mb_substr($title, $i, 1, 'UTF-8');
            $proposedText = $wrapTitle.$currentChar;

            if ($this->calculateTextWidth($proposedText) < $maxWidth) {
                $wrapTitle .= $currentChar;
                continue;
            }

            $wrapTitle = trim($wrapTitle);
            $wrapTitle .= PHP_EOL.$currentChar;
        }

        return $wrapTitle;
    }

    private function calculateTextWidth(string $text): int
    {
        $bbox = imagettfbbox(
            size: $this->fontSize,
            angle: 0,
            font_filename: $this->fontPath,
            string: $text
        );

        return $bbox[2] - $bbox[0];
    }

    public function output(): void
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $backgroundColor = imagecolorallocate($this->image,
            ...$this->converter->hexToRgb($this->imageBackgroundColor));
        imagefill($this->image, 0, 0, $backgroundColor);

        $wrapTitle = $this->wrapTitle($this->title);

        $titleBbox = imagettfbbox($this->fontSize, 0, $this->fontPath, $wrapTitle);
        $titleHeight = $titleBbox[1] - $titleBbox[5];

        $x = round($this->width * self::TEXT_MARGIN_RATIO);
        $y = round((imagesy($this->image) - $titleHeight) / 2 - $titleBbox[5]);
        $titleColor = imagecolorallocate($this->image, ...$this->converter->hexToRgb($this->titleColor));
        imagettftext($this->image, $this->fontSize, 0, $x, $y, $titleColor, $this->fontPath, $wrapTitle);

        header('Content-Type: image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }
}
