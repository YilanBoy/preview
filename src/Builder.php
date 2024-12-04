<?php

namespace Yilanboy\Preview;

use GdImage;

class Builder
{
    private const TEXT_MARGIN_RATIO = 0.05;

    public int $width = 1200;

    public int $height = 628;

    public array $header = [
        'text' => 'Preview',
        'font_path' => __DIR__.'/../fonts/noto-sans-tc.ttf',
        'font_size' => 75,
        'color' => '#030712',
    ];

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

    public function header(
        string $text,
        string $color = 'black',
        int $fontSize = 75,
        string $fontPath = __DIR__.'/../fonts/noto-sans-tc.ttf',
    ): static {
        $this->header['text'] = $text;

        if ($color[0] !== '#') {
            $this->header['color'] = $this->converter->nameToHex($color);
        } else {
            $this->header['color'] = $color;
        }

        $this->header['font_size'] = $fontSize;

        $this->header['font_path'] = $fontPath;

        return $this;
    }

    private function splitStringToArray($input): array
    {
        preg_match_all('/\p{Han}|[a-zA-Z0-9]+|\s|[^\p{Han}\s\w]/u', $input, $matches);

        return $matches[0];
    }

    private function wrapTextImage(
        string $text,
        string $fontSize,
        string $fontPath
    ): string {
        $wrapText = '';
        $words = $this->splitStringToArray($text);
        $length = count($words);
        // The maximum width should subtract the width of the border on both sides.
        $maxWidth = round($this->width * (1 - (self::TEXT_MARGIN_RATIO * 2)));

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

    private static function calculateTextImageWidth(
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

    private function configureHeader(): void
    {
        $wrapHeader = $this->wrapTextImage(
            $this->header['text'], $this->header['font_size'], $this->header['font_path']);

        $headerBbox = imagettfbbox(
            $this->header['font_size'], 0, $this->header['font_path'], $wrapHeader);

        $headerHeight = $headerBbox[1] - $headerBbox[5];

        $x = round($this->width * self::TEXT_MARGIN_RATIO);
        $y = round(imagesy($this->image) / 3 - $headerHeight / 2);

        $headerColor = imagecolorallocate(
            $this->image, ...$this->converter->hexToRgb($this->header['color']));

        imagettftext(
            image: $this->image,
            size: $this->header['font_size'],
            angle: 0,
            x: $x,
            y: $y,
            color: $headerColor,
            font_filename: $this->header['font_path'],
            text: $wrapHeader
        );
    }

    private function configureTitle(): void
    {
        $wrapTitle = $this->wrapTextImage(
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
        $this->configureHeader();
        $this->configureTitle();

        header('Content-Type: image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }

    public function save(string $path): void
    {
        $this->configureCanvas();
        $this->configureHeader();
        $this->configureTitle();

        imagepng($this->image, $path);
        imagedestroy($this->image);
    }
}
