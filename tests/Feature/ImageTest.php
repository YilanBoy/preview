<?php

it('can output image', function () {
    $headers = get_headers('http://localhost:8000/', true);

    expect($headers[0])->toBe('HTTP/1.1 200 OK')
        ->and($headers['Content-Type'])->toBe('image/png');
});
