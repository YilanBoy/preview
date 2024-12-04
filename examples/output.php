<?php

include_once __DIR__.'/../vendor/autoload.php';

use Yilanboy\Preview\Images\Builder;

(new Builder())
    ->size(width: 1200, height: 628)
    ->backgroundColor('#777bb3')
    ->header(text: 'Preview', color: 'white')
    ->title(text: 'A simple PHP package to create preview image', color: 'white')
    ->output();
