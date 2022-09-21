# php-polylabel
PHP port of https://github.com/mapbox/polylabel

## Usage

```php
$polygon = [[0, 0], [1, 0], [1, 1], [0, 1], [0, 0]];

$polylabel = new Relocity\PhpPolylabel\Polylabel;
$center = $polylabel->getCenter($polygon); // ['x' => 0.5, 'y' => 0.5, 'distance' => 0.5]
```