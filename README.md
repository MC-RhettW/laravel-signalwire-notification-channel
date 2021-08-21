# Laravel Signalwire Notification Channel

<a href="https://github.com/laravel/SignalWire-notification-channel/actions"><img src="https://github.com/laravel/SignalWire-notification-channel/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/SignalWire-notification-channel"><img src="https://img.shields.io/packagist/dt/laravel/SignalWire-notification-channel" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/SignalWire-notification-channel"><img src="https://img.shields.io/packagist/v/laravel/SignalWire-notification-channel" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/SignalWire-notification-channel"><img src="https://img.shields.io/packagist/l/laravel/SignalWire-notification-channel" alt="License"></a>

## Credits

### Mankato Clinic
Open-source MCDev software and components are developed and by the small but dedicated programming team at [Mankato Clinic](http://www.mankatoclinic.com/), a doctor-owned multi-specialty healthcare provider in southern Minnesota, USA. See more in the [CREDITS](CREDITS.md) documentation page for more information.

### Upstream Contributor Credits

MCDev software, including this package, has benefited from the work of many incredible individual and organizational open-source contributors and the open-source user community. For a list of just some of the major contributors that have empowered our work, please see the [CREDITS](CREDITS.md) documentation page.

## Installation

You can install the package via composer:

```bash
composer require mcdev/laravel-signalwire-notification-channel
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="MCDev\Notifications\SignalWireChannelServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    'from'=>env('SMS_FROM',''),
    'credentials'=>[
        env('SIGNALWIRE_API_PROJECT'),
        env('SIGNALWIRE_API_TOKEN')
    ],
    'log_success' => 'info'
];
```

## Usage

```php
$example = new MCDev\Laraskel();
echo $example->shoutItOut('Hello, world!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on recent changes. We try to include both MCDev and upstream change notes.

## Contributing

Code contributions and feature ideas are welcome! Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## License

This package is published under the MIT License (MIT) and intended to be free to use in both commercial and non-commercial projects. 

Please see the [LICENSE](LICENSE.md) document for more information.
