# Preview

A simple package to generate preview image.

```php
use Yilanboy\Preview\Image\Builder;

(new Builder())
    ->size(width: 1200, height: 628)
    ->backgroundColor('#777bb3')
    ->header(text: 'Preview', color: 'white')
    ->title(text: 'A simple PHP package to create preview image', color: 'white')
    ->output();
```

This code will display the following image on the web page.

![preview](images/preview.png)

## Start a Local Server to Show the Image

There is a `output.php` file in `examples` folder, you can simply start a local server to see the image in browser.

```bash
php -S localhost:8000 examples/output.php
```

Then open your browser and visit [localhost:8000](http://localhost:8000).

## Run Tests

Before running the test, please start the local web server.

```bash
composer run-server
composer run-tests
```
