<?php

namespace Yilanboy\Preview\Images;

use GdImage;
use Yilanboy\Preview\ColorConverter;

final class Builder
{
    private const DEFAULT_FONT_PATH = __DIR__.'/../../fonts/noto-sans-tc.ttf';

    private const MARGIN_RATIO = 0.05;

    public int $width = 1200;

    public int $height = 628;

    public array $header = [
        'text' => 'Preview',
        'font_path' => self::DEFAULT_FONT_PATH,
        'font_size' => 75,
        'color' => '#030712',
    ];

    public array $title = [
        'text' => 'Hello World!',
        'font_path' => self::DEFAULT_FONT_PATH,
        'font_size' => 50,
        'color' => '#030712',
    ];

    public string $backgroundColor = '#f9fafb';

    public false|GdImage $image;

    public function __construct(
        public ColorConverter $converter = new ColorConverter(),
        public TextHandler $handler = new TextHandler()
    ) {
    }

    public function size(int $width, int $height): Builder
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function backgroundColor(string $color): Builder
    {
        if ($color[0] !== '#') {
            $color = $this->converter->nameToHex($color);
        }

        $this->backgroundColor = $color;

        return $this;
    }

    public function title(
        string $text,
        ?string $color = null,
        ?int $fontSize = null,
        ?string $fontPath = null,
    ): Builder {
        $this->title['text'] = $text;

        if (! is_null($color)) {
            if ($color[0] !== '#') {
                $this->title['color'] = $this->converter->nameToHex($color);
            } else {
                $this->title['color'] = $color;
            }
        }

        if (! is_null($fontSize)) {
            $this->title['font_size'] = $fontSize;
        }

        if (! is_null($fontPath)) {
            $this->title['font_path'] = $fontPath;
        }

        return $this;
    }

    public function header(
        string $text,
        ?string $color = null,
        ?int $fontSize = null,
        ?string $fontPath = null,
    ): Builder {
        $this->header['text'] = $text;

        if (! is_null($color)) {
            if ($color[0] !== '#') {
                $this->header['color'] = $this->converter->nameToHex($color);
            } else {
                $this->header['color'] = $color;
            }
        }

        if (! is_null($fontSize)) {
            $this->header['font_size'] = $fontSize;
        }

        if (! is_null($fontPath)) {
            $this->header['font_path'] = $fontPath;
        }

        return $this;
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
        $wrapHeader = $this->handler->wrapTextImage(
            text: $this->header['text'],
            fontSize: $this->header['font_size'],
            fontPath: $this->header['font_path'],
            // The maximum width should subtract the width of the border on both sides.
            maxWidth: $this->width - $this->width * self::MARGIN_RATIO * 2
        );

        $headerBbox = imagettfbbox(
            $this->header['font_size'], 0, $this->header['font_path'], $wrapHeader);

        $headerHeight = $headerBbox[1] - $headerBbox[5];

        $x = round($this->width * self::MARGIN_RATIO);
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
        $wrapTitle = $this->handler->wrapTextImage(
            text: $this->title['text'],
            fontSize: $this->title['font_size'],
            fontPath: $this->title['font_path'],
            maxWidth: $this->width - $this->width * self::MARGIN_RATIO * 2
        );

        $titleBbox = imagettfbbox(
            $this->title['font_size'], 0, $this->title['font_path'], $wrapTitle);

        $titleHeight = $titleBbox[1] - $titleBbox[5];

        $x = round($this->width * self::MARGIN_RATIO);
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
