<?php

namespace Yilanboy\Preview;

class Generator
{
    public int $width = 1200;

    public int $height = 628;

    public int $fontSize = 100;

    public string $text = '你好！';

    public string $backGroundHexColorCode = '#059669';

    public string $textHexColorCode = '#ffffff';

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

    public static function hexToRgb($hex): array
    {
        return sscanf($hex, '#%02x%02x%02x');
    }

    public function output(): void
    {
        $image = imagecreatetruecolor($this->width, $this->height);

        $backgroundColor = imagecolorallocate($image, ...$this->hexToRgb($this->backGroundHexColorCode));

        imagefill($image, 0, 0, $backgroundColor);

        $textColor = imagecolorallocate($image, ...$this->hexToRgb($this->textHexColorCode));

        $fontPath = dirname(__FILE__).'/../fonts/noto-sans-tc.ttf';

        $bbox = imagettfbbox($this->fontSize, 0, $fontPath, $this->text);

        $width = $bbox[2] - $bbox[0];
        $height = $bbox[1] - $bbox[5];

        $x = (imagesx($image) - $width) / 2;
        $y = (imagesy($image) - $height) / 2 - $bbox[5];

        imagettftext($image, $this->fontSize, 0, $x, $y, $textColor, $fontPath, $this->text);

        header('Content-Type: image/png');

        imagepng($image);

        imagedestroy($image);
    }
}
