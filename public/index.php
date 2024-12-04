<?php

include_once dirname(__FILE__).'/../vendor/autoload.php';

use Yilanboy\Preview\Builder;

(new Builder())
    ->size(width: 1200, height: 628)
    ->backgroundColor('#10b981')
    ->title(text: 'A true master is an eternal student', color: 'white')
    ->output();
