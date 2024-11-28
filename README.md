# Preview

A simple package to generate preview image.

```php
use Yilanboy\Preview\Generator;

new Generator()
    ->size(width: 1200, height: 628)
    ->backGroundHexColorCode('#10b981')
    ->textHexColorCode('#f9fafb')
    ->fontSize(50)
    ->text('Hello World!')
    ->output();
```
