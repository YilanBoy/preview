<?php

include_once 'vendor/autoload.php';

use Yilanboy\Preview\Generator;

(new Generator())
    ->size(width: 1200, height: 628)
    ->backgroundColor('#10b981')
    ->titleColor('#f9fafb')
    ->fontSize(50)
    ->title('A true master is an eternal student')
    ->output();
