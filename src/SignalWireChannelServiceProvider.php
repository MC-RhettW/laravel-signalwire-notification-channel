<?php

namespace MCDev\Notifications;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use MCDev\Notifications\Channels\SignalWireChannel;
use SignalWire\Relay\Client;

class SignalWireChannelServiceProvider extends ServiceProvider
{

    /**
     * Service provider bootstrap
     *
     * @return void
     */
    public function boot()
    {
        // Allow config file publication from Artisan console
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/signalwire.php' => $this->app->configPath('signalwire.php'),
            ], 'config');
        }
    }

    /**
     * Service provider registration
     *
     * @return void
     */
    public function register()
    {
        // Merge local and vendor config files
        $this->mergeConfigFrom(__DIR__.'/../config/signalwire.php', 'signalwire');

        // Register the notification channel
        Notification::resolved(function (ChannelManager $service) {
            // Setup SignalWire channel(s)
            $swChannelCallback = function () {
                return new SignalWireChannel(
                    $this->app->make(Client::class, Config::get('signalwire.credentials')),
                    Config::get('services.signalwire.from')
                );
            };

            // SignalWire-specific channel
            $service->extend('SignalWire', $swChannelCallback);

            // Register SignalWire as the Sms channel, if none has been registered
            if (!array_key_exists('Sms', $service->getDrivers())) {
                $service->extend('Sms', $swChannelCallback);
            }
        });
    }
}
