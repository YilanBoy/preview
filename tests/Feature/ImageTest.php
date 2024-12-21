<?php

use Yilanboy\Preview\Image\Builder;

it('can save png image', function () {
    $filename = 'test.png';

    (new Builder())
        ->size(width: 1200, height: 600)
        ->backgroundColor('#10b981')
        ->title(text: 'A true master is an eternal student', color: 'white')
        ->save($filename);

    expect(file_exists($filename))->toBeTrue();
    unlink($filename);
});
