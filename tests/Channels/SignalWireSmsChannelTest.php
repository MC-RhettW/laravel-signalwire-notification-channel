<?php

namespace Illuminate\Tests\Notifications\Channels;

use Illuminate\Notifications\Channels\SignalWireSmsChannel;
use Illuminate\Notifications\Messages\SignalWireMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Tests\Notifications\TestCase;
use Mockery as m;
use SignalWire\Client;

class SignalWireSmsChannelTest extends TestCase
{
    public function testSmsIsSentViaSignalWire()
    {
        $notification = new NotificationSignalWireSmsChannelTestNotification;
        $notifiable = new NotificationSignalWireSmsChannelTestNotifiable;

        $channel = new SignalWireSmsChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldReceive('message->send')
            ->with([
                'type' => 'text',
                'from' => '4444444444',
                'to' => '5555555555',
                'text' => 'this is my message',
                'client-ref' => '',
            ])
            ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaSignalWireWithCustomClient()
    {
        $customSignalWire = m::mock(Client::class);
        $customSignalWire->shouldReceive('message->send')
            ->with([
                'type' => 'text',
                'from' => '4444444444',
                'to' => '5555555555',
                'text' => 'this is my message',
                'client-ref' => '',
            ])
            ->once();

        $notification = new NotificationSignalWireSmsChannelTestCustomClientNotification($customSignalWire);
        $notifiable = new NotificationSignalWireSmsChannelTestNotifiable;

        $channel = new SignalWireSmsChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldNotReceive('message->send');

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaSignalWireWithCustomFrom()
    {
        $notification = new NotificationSignalWireSmsChannelTestCustomFromNotification;
        $notifiable = new NotificationSignalWireSmsChannelTestNotifiable;

        $channel = new SignalWireSmsChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldReceive('message->send')
            ->with([
                'type' => 'unicode',
                'from' => '5554443333',
                'to' => '5555555555',
                'text' => 'this is my message',
                'client-ref' => '',
            ])
            ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaSignalWireWithCustomFromAndClient()
    {
        $customSignalWire = m::mock(Client::class);
        $customSignalWire->shouldReceive('message->send')
            ->with([
                'type' => 'unicode',
                'from' => '5554443333',
                'to' => '5555555555',
                'text' => 'this is my message',
                'client-ref' => '',
            ])
            ->once();

        $notification = new NotificationSignalWireSmsChannelTestCustomFromAndClientNotification($customSignalWire);
        $notifiable = new NotificationSignalWireSmsChannelTestNotifiable;

        $channel = new SignalWireSmsChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldNotReceive('message->send');

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaSignalWireWithCustomFromAndClientRef()
    {
        $notification = new NotificationSignalWireSmsChannelTestCustomFromAndClientRefNotification;
        $notifiable = new NotificationSignalWireSmsChannelTestNotifiable;

        $channel = new SignalWireSmsChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldReceive('message->send')
            ->with([
                'type' => 'unicode',
                'from' => '5554443333',
                'to' => '5555555555',
                'text' => 'this is my message',
                'client-ref' => '11',
            ])
            ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaSignalWireWithCustomClientFromAndClientRef()
    {
        $customSignalWire = m::mock(Client::class);
        $customSignalWire->shouldReceive('message->send')
            ->with([
                'type' => 'unicode',
                'from' => '5554443333',
                'to' => '5555555555',
                'text' => 'this is my message',
                'client-ref' => '11',
            ])
            ->once();

        $notification = new NotificationSignalWireSmsChannelTestCustomClientFromAndClientRefNotification($customSignalWire);
        $notifiable = new NotificationSignalWireSmsChannelTestNotifiable;

        $channel = new SignalWireSmsChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldNotReceive('message->send');

        $channel->send($notifiable, $notification);
    }

    public function testCallbackIsApplied()
    {
        $notification = new NotificationSignalWireSmsChannelTestCallback;
        $notifiable = new NotificationSignalWireSmsChannelTestNotifiable;

        $channel = new SignalWireSmsChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldReceive('message->send')
            ->with([
                'type' => 'text',
                'from' => '4444444444',
                'to' => '5555555555',
                'text' => 'this is my message',
                'client-ref' => '',
                'callback' => 'https://example.com',
            ])
            ->once();

        $channel->send($notifiable, $notification);
    }
}

class NotificationSignalWireSmsChannelTestNotifiable
{
    use Notifiable;

    public $phone_number = '5555555555';

    public function routeNotificationForSignalWire($notification)
    {
        return $this->phone_number;
    }
}

class NotificationSignalWireSmsChannelTestNotification extends Notification
{
    public function toSignalWire($notifiable)
    {
        return new SignalWireMessage('this is my message');
    }
}

class NotificationSignalWireSmsChannelTestCustomClientNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toSignalWire($notifiable)
    {
        return (new SignalWireMessage('this is my message'))->usingClient($this->client);
    }
}

class NotificationSignalWireSmsChannelTestCustomFromNotification extends Notification
{
    public function toSignalWire($notifiable)
    {
        return (new SignalWireMessage('this is my message'))->from('5554443333')->unicode();
    }
}

class NotificationSignalWireSmsChannelTestCustomFromAndClientNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toSignalWire($notifiable)
    {
        return (new SignalWireMessage('this is my message'))->from('5554443333')->unicode()->usingClient($this->client);
    }
}

class NotificationSignalWireSmsChannelTestCustomFromAndClientRefNotification extends Notification
{
    public function toSignalWire($notifiable)
    {
        return (new SignalWireMessage('this is my message'))->from('5554443333')->unicode()->clientReference('11');
    }
}

class NotificationSignalWireSmsChannelTestCustomClientFromAndClientRefNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toSignalWire($notifiable)
    {
        return (new SignalWireMessage('this is my message'))
            ->from('5554443333')
            ->unicode()
            ->clientReference('11')
            ->usingClient($this->client);
    }
}

class NotificationSignalWireSmsChannelTestCallback extends Notification
{
    public function toSignalWire($notifiable)
    {
        return (new SignalWireMessage('this is my message'))
            ->statusCallback('https://example.com');
    }
}
