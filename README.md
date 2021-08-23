# SignalWire Notification Channel for Laravel

<a href="https://github.com/MankatoClinic/laravel-signalwire-notification-channel/actions"><img src="https://github.com/MankatoClinic/laravel-signalwire-notification-channel/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/mcdev/laravel-signalwire-notification-channel"><img src="https://img.shields.io/packagist/dt/mcdev/laravel-signalwire-notification-channel" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/mcdev/laravel-signalwire-notification-channel"><img src="https://img.shields.io/packagist/v/mcdev/laravel-signalwire-notification-channel" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/mcdev/laravel-signalwire-notification-channel"><img src="https://img.shields.io/packagist/l/mcdev/laravel-signalwire-notification-channel" alt="License"></a>

This package facilitate the use of the [SignalWire](https://www.signalwire.com/) messaging provider for sending
notifications and message using standard Laravel conventions.

It was forked from the official Laravel [Nexmo Notification Channel](https://github.com/laravel/nexmo-notification-channel) and intended to provide
feature-parity so that SignalWire can be used as drop-in alternative for sending SMS notifications using standard,
well-supported Laravel conventions.

## Installation

This package can be installed via Composer:

```bash
composer require mcdev/laravel-signalwire-notification-channel
```

## Configuration

Channel behavior can be configured using a local configuration file, environment configuration entries, or both.

A local config can be published via Artisan:

```bash
php artisan vendor:publish --provider="MCDev\Notifications\SignalWireChannelServiceProvider" --tag="config"
```

Here are the contents of the published config file:

```php

return [

    // The default phone number to send from
    'from'=>env('SMS_FROM'),

    // API credentials defined in your SignalWire space
    'credentials'=>[
        env('SIGNALWIRE_API_PROJECT'),  // 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'
        env('SIGNALWIRE_API_TOKEN')     // 'PTXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'
    ],

    // Application log output settings
    'log'=>[
        'include_context' => env('SIGNALWIRE_LOG_CONTEXT',true), // Include a verbose context object in log entries where possible
        'success_logging' => env('SIGNALWIRE_LOG_SUCCESSFUL','info') // Log level for successfully sent messages (NULL or FALSE disables)
    ]

];

```

## Usage

### Notification Example

```php

namespace App\Notifications;

use MCDev\Notifications\Messages\SignalWireMessage;
use MCDev\Notificiations\Channels\SignalWireChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class HelloAndWelcome extends Notification
{

    use Queueable;

    // ...

    /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [
            SignalWireNotificationChannel::class,
            // ...
        ];
    }

    /**
     * Get SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SignalWireMessage
     */
    public function toSignalWire($notifiable)
    {
        return (new SignalWireMessage)
                    ->content(__('Hello and welcome! This is a notification sent using SignalWire.'));
    }

    // ...

}

```

### Notifiable Example

```php

namespace App\Models;

use Illuminate\Notifications\Notifiable;

class Contact
{
    use Notifiable;

    /**
     * Route notifications for the Nexmo channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForSignalWire($notification)
    {
        return $this->cellPhone ?? $this->mobilePhone ?? $this->phoneNumber;
    }

    // ...

}

```

## Testing

```bash
composer test
```

## Credits

### Mankato Clinic

Open-source MCDev software and components are developed and by the small but dedicated programming team
at [Mankato Clinic](http://www.mankatoclinic.com/), a doctor-owned and community-driven multi-specialty healthcare
provider serving southern Minnesota, USA.

### Upstream Contributor Credits

MCDev software, including this package, has benefited from the work of many incredible individual and organizational
open-source contributors and the open-source user community.

Major external and upstream contributions for this package have come from:

- (Laravel Nexmo Notification Channel)[https://github.com/laravel/nexmo-notification-channel] by (Taylor Otwell)[https://github.com/taylorotwell] and (the Laravel team)[https://laravel.com/team]

## Changelog

Please see the [CHANGELOG](CHANGELOG.md) for more information on recent changes. We try to include both MCDev and
upstream change notes.

## Contributing

Code contributions, bug reports and feature ideas are welcome. Please see the [CONTRIBUTING](.github/CONTRIBUTING.md)
page for more information.

## Security

Please see this project [security page on Github](../../security/policy) for security notices and vulnerability
reporting information.

## License

This is an open-source package is released under the MIT License (MIT). It is free to use in both commercial and
non-commercial projects.

Please see the [LICENSE](LICENSE.md) document for more information.
