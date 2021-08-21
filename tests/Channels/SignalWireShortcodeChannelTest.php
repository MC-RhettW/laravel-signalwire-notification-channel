<?php

namespace Illuminate\Tests\Notifications\Channels;

use Illuminate\Notifications\Channels\SignalWireShortcodeChannel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Tests\Notifications\TestCase;
use Mockery as m;
use SignalWire\Message\Client;

class SignalWireShortcodeChannelTest extends TestCase
{
    public function testShortcodeIsSentViaSignalWire()
    {
        $notification = new NotificationSignalWireShortcodeChannelTestNotification;
        $notifiable = new NotificationSignalWireShortcodeChannelTestNotifiable;

        $channel = new SignalWireShortcodeChannel(
            $SignalWire = m::mock(Client::class)
        );

        $SignalWire->shouldReceive('sendShortcode')
            ->with([
                'type' => 'alert',
                'to' => '5555555555',
                'custom' => [
                    'code' => 'abc123',
                ],
            ])
            ->once();

        $channel->send($notifiable, $notification);
    }
}

class NotificationSignalWireShortcodeChannelTestNotifiable
{
    use Notifiable;

    public $phone_number = '5555555555';

    public function routeNotificationForShortcode($notification)
    {
        return $this->phone_number;
    }
}

class NotificationSignalWireShortcodeChannelTestNotification extends Notification
{
    public function toShortcode($notifiable)
    {
        return [
            'type' => 'alert',
            'custom' => [
                'code' => 'abc123',
            ],
        ];
    }
}
