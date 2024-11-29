<?php

namespace Yilanboy\Preview;

class Generator
{
    public int $width = 1200;

    public int $height = 628;

    public int $fontSize = 100;

    public string $fontPath;

    public string $text = 'Hello World!';

    public string $backGroundHexColorCode = '#059669';

    public string $textHexColorCode = '#ffffff';

    public Converter $converter;

    public function __construct()
    {
        $this->converter = new Converter;
        $this->fontPath = dirname(__FILE__).'/../fonts/noto-sans-tc.ttf';
    }

    public function size(int $width, int $height): static
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function backGroundHexColorCode(string $backGroundHexColorCode): static
    {
        $this->backGroundHexColorCode = $backGroundHexColorCode;

        return $this;
    }

    public function textHexColorCode(string $textHexColorCode): static
    {
        $this->textHexColorCode = $textHexColorCode;

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

    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function output(): void
    {
        $image = imagecreatetruecolor($this->width, $this->height);
        $backgroundColor = imagecolorallocate($image, ...$this->converter->hexToRgb($this->backGroundHexColorCode));
        imagefill($image, 0, 0, $backgroundColor);

        $textColor = imagecolorallocate($image, ...$this->converter->hexToRgb($this->textHexColorCode));

        $bbox = imagettfbbox($this->fontSize, 0, $this->fontPath, $this->text);

        $width = $bbox[2] - $bbox[0];
        $height = $bbox[1] - $bbox[5];

        $x = (imagesx($image) - $width) / 2;
        $y = (imagesy($image) - $height) / 2 - $bbox[5];

        imagettftext($image, $this->fontSize, 0, $x, $y, $textColor, $this->fontPath, $this->text);

        header('Content-Type: image/png');

        imagepng($image);

        imagedestroy($image);
    }
}
