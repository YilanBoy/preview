# Preview

A simple package to generate preview image.

```php
use Yilanboy\Preview\Generator;

(new Generator())
    ->size(width: 1200, height: 628)
    ->backgroundColor('#10b981')
    ->titleColor('white')
    ->fontSize(50)
    ->title('A true master is an eternal student')
    ->output();
```

## Start a Local Server to Show the Image

There is a `server.php` file in repo root directory, you can simply start a local server to see the image in browser.

```bash
composer run-server
```

Then open your browser and visit [localhost:8000](http://localhost:8000).

## Run Tests

Before running the test, please start the local web server.

```bash
composer run-server
composer run-tests
```
