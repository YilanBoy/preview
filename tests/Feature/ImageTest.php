<?php

use Yilanboy\Preview\Builder;

it('can output image', function () {
    $headers = get_headers('http://localhost:8000/', true);

    expect($headers[0])->toBe('HTTP/1.1 200 OK')
        ->and($headers['Content-Type'])->toBe('image/png');
});

it('can save image', function () {
    $filename = 'test.png';

    (new Builder())
        ->size(width: 1200, height: 628)
        ->backgroundColor('#10b981')
        ->title(text: 'A true master is an eternal student', color: 'white')
        ->save($filename);

    expect(file_exists($filename))->toBeTrue();
    unlink($filename);
});
