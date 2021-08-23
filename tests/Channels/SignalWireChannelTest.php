<?php

namespace MCDev\Tests\Notifications\Channels;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use MCDev\Notifications\Channels\SignalWireChannel;
use MCDev\Notifications\Messages\SignalWireMessage;
use MCDev\Tests\Notifications\TestCase;
use Mockery as m;
use SignalWire\Relay\Client;

class SignalWireChannelTest extends TestCase
{
    public function testMsgSentViaSignalWire(): void
    {
        $notification = new NotificationSignalWireChannelTestNotification;
        $notifiable = new NotificationSignalWireChannelTestNotifiable;

        $channel = new SignalWireChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldReceive('message->send')
            ->with([
                'context' => 'notifications',
                'from' => '4444444444',
                'to' => '5555555555',
                'text' => 'Testing 1-2-3, from the SignalWire channel test suite.',
                'tags' => 'test-tag1'
            ])
            ->once();

        $channel->send($notifiable, $notification);
    }

    public function testMsgSentViaSignalWireWithCustomClient(): void
    {
        $customSignalWire = m::mock(Client::class);
        $customSignalWire->shouldReceive('message->send')
            ->with([
                'type' => 'text',
                'from' => '4444444444',
                'to' => '5555555555',
                'text' => 'Testing 1-2-3, from the SignalWire channel test suite.',
                'tags' => 'test-tag1'
            ])
            ->once();

        $notification = new NotificationSignalWireChannelTestCustomClientNotification($customSignalWire);
        $notifiable = new NotificationSignalWireChannelTestNotifiable;

        $channel = new SignalWireChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldNotReceive('message->send');

        $channel->send($notifiable, $notification);
    }

    public function testMsgSentViaSignalWireWithCustomFrom(): void
    {
        $notification = new NotificationSignalWireChannelTestCustomFromNotification;
        $notifiable = new NotificationSignalWireChannelTestNotifiable;

        $channel = new SignalWireChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldReceive('message->send')
            ->with([
                'context' => 'notifications',
                'from' => '5554443333',
                'to' => '5555555555',
                'text' => 'Testing 1-2-3, from the SignalWire channel test suite.',
                'tags' => []
            ])
            ->once();

        $channel->send($notifiable, $notification);
    }

    public function testMsgSentViaSignalWireWithCustomFromAndClient(): void
    {
        $customSignalWire = m::mock(Client::class);
        $customSignalWire->shouldReceive('message->send')
            ->with([
                'context' => 'notifications',
                'from' => '5554443333',
                'to' => '5555555555',
                'text' => 'Testing 1-2-3, from the SignalWire channel test suite.',
                'tags' => []
            ])
            ->once();

        $notification = new NotificationSignalWireChannelTestCustomFromAndClientNotification($customSignalWire);
        $notifiable = new NotificationSignalWireChannelTestNotifiable;

        $channel = new SignalWireChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldNotReceive('message->send');

        $channel->send($notifiable, $notification);
    }

    public function testMsgSentViaSignalWireWithCustomFromAndTags(): void
    {
        $notification = new NotificationSignalWireChannelTestCustomTagsFromAndContextNotification;
        $notifiable = new NotificationSignalWireChannelTestNotifiable;

        $channel = new SignalWireChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldReceive('message->send')
            ->with([
                'context' => 'notifications',
                'from' => '5554443333',
                'to' => '5555555555',
                'text' => 'Testing 1-2-3, from the SignalWire channel test suite.',
                'tags' => ['tag1', 'tag2']
            ])
            ->once();

        $channel->send($notifiable, $notification);
    }

    public function testMsgSentViaSignalWireWithCustomClientFromAndContext(): void
    {
        $customSignalWire = m::mock(Client::class);
        $customSignalWire->shouldReceive('message->send')
            ->with([
                'context' => 'custom-notifications',
                'from' => '5554443333',
                'to' => '5555555555',
                'text' => 'Testing 1-2-3, from the SignalWire channel test suite.',
                'tags' => []
            ])
            ->once();

        $notification = new NotificationSignalWireChannelTestCustomClientFromAndContextNotification($customSignalWire);
        $notifiable = new NotificationSignalWireChannelTestNotifiable;

        $channel = new SignalWireChannel(
            $SignalWire = m::mock(Client::class), '4444444444'
        );

        $SignalWire->shouldNotReceive('message->send');

        $channel->send($notifiable, $notification);
    }

}

class NotificationSignalWireChannelTestNotifiable
{
    use Notifiable;

    public $phone_number = '5555555555';

    public function routeNotificationForSignalWire($notification): string
    {
        return $this->phone_number;
    }
}

class NotificationSignalWireChannelTestNotification extends Notification
{
    public function toSignalWire($notifiable): SignalWireMessage
    {
        return new SignalWireMessage('Testing 1-2-3, from the SignalWire channel test suite.');
    }
}

class NotificationSignalWireChannelTestCustomClientNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toSignalWire($notifiable): SignalWireMessage
    {
        return (new SignalWireMessage('Testing 1-2-3, from the SignalWire channel test suite.'))
            ->usingClient($this->client);
    }
}

class NotificationSignalWireChannelTestCustomFromNotification extends Notification
{
    public function toSignalWire($notifiable): SignalWireMessage
    {
        return (new SignalWireMessage('Testing 1-2-3, from the SignalWire channel test suite.'))
            ->from('5554443333');
    }
}

class NotificationSignalWireChannelTestCustomFromAndClientNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toSignalWire($notifiable): SignalWireMessage
    {
        return (new SignalWireMessage('Testing 1-2-3, from the SignalWire channel test suite.'))
            ->from('5554443333')
            ->usingClient($this->client);
    }
}

class NotificationSignalWireChannelTestCustomTagsFromAndContextNotification extends Notification
{
    public function toSignalWire($notifiable): SignalWireMessage
    {
        return (new SignalWireMessage('Testing 1-2-3, from the SignalWire channel test suite.'))
            ->from('5554443333')
            ->withTags(['tag1', 'tag2'])
            ->withContext('custom-context');
    }
}

class NotificationSignalWireChannelTestCustomClientFromAndContextNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toSignalWire($notifiable): SignalWireMessage
    {
        return (new SignalWireMessage('Testing 1-2-3, from the SignalWire channel test suite.'))
            ->from('5554443333')
            ->withContext('custom-context')
            ->usingClient($this->client);
    }
}
