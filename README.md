# container

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

An amazing, easy to follow PHP dependency injection container package. Made with PSR-2 and PSR-4.

## Install

Via Composer

``` bash
$ composer require martiadrogue/container
```

## Usage

``` php
$parameters = [
    'first' => [
        'second' => 'foo',
        'third' => [
            'fourth' => 'bar',
        ],
    ],
];
$container = new MartiAdrogue\Container([], parameters);
echo $container->getParameter('first.third.fourth');
```

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email marti.adrogue@gmail.com instead of using the issue tracker.

## Credits

- [Martí Adrogué][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/martiadrogue/container.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/martiadrogue/container.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/martiadrogue/container.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/martiadrogue/container.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/martiadrogue/container
[link-scrutinizer]: https://scrutinizer-ci.com/g/martiadrogue/container/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/martiadrogue/container
[link-downloads]: https://packagist.org/packages/martiadrogue/container
[link-author]: https://github.com/martiadrogue
[link-contributors]: ../../contributors
