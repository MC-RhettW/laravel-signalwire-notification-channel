<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

/** @noinspection UnusedConstructorDependenciesInspection */

/** @noinspection PhpUnusedParameterInspection */

namespace MCDev\Tests\Notifications\Channels;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use MCDev\Notifications\Channels\SignalWireChannel;
use MCDev\Notifications\Messages\SignalWireMessage;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SignalWire\Rest\Client;

class SignalWireChannelTest extends TestCase
{

    use MockeryPHPUnitIntegration;

    private $defaultContent = 'Testing 1-2-3, from the SignalWire channel test suite.';
    private $defaultFrom = '4444444444';
    private $defaultClient;
    private $notice;
    private $contact;

    private function init() {
        $this->defaultClient = m::mock(Client::class);
        $this->notice = new SignalWireTestNotification();
        $this->contact = new SignalWireTestNotifiable();
    }

    public function testMsgSentViaSignalWire(): void
    {

        $this->init();
        $this->defaultClient->shouldReceive('messages->create')->once();

        $channel = new SignalWireChannel($this->defaultClient, $this->defaultFrom);
        $channel->send($this->contact, $this->notice);

    }

    public function testMsgSentViaSignalWireWithCustomClient(): void
    {
        $this->init();
        $custom = m::mock(Client::class);

        $notification = new NotificationSignalWireChannelTestCustomClientNotification($custom);
        $notifiable = new SignalWireTestNotifiable;

        $custom->shouldReceive('messages->create')->once();
        $this->defaultClient->shouldNotReceive('messages->create');

        $channel = new SignalWireChannel($custom, $this->defaultFrom);
        $channel->send($notifiable, $notification);

    }

    public function testMsgSentViaSignalWireWithCustomFrom(): void
    {
        $signalwire = m::mock(Client::class);
        $notification = new NotificationSignalWireChannelTestCustomFromNotification;
        $notifiable = new SignalWireTestNotifiable;

        $channel = new SignalWireChannel($signalwire, '4444444444');

        $signalwire->shouldReceive('messages->create')->once();

        $channel->send($notifiable, $notification);
    }

    public function testMsgSentViaSignalWireWithCustomFromAndClient(): void
    {
        $this->init();
        $custom = m::mock(Client::class);

        $notification = new NotificationSignalWireChannelTestCustomFromAndClientNotification($custom);
        $notifiable = new SignalWireTestNotifiable;

        $channel = new SignalWireChannel($custom, '4444444444');

        $custom->shouldReceive('messages->create')->once();
        $this->defaultClient->shouldNotReceive('messages->create');

        $channel->send($notifiable, $notification);

    }

    public function tearDown(): void
    {
        m::close();
    }

}

class SignalWireTestNotifiable
{
    use Notifiable;

    public $phone_number = '5555555555';

    public function routeNotificationForSignalWire($notification): string
    {
        return $this->phone_number;
    }
}

class SignalWireTestNotification extends Notification
{

    private $client;

    public function __construct(Client $client=null)
    {
        if (null !== $client) {
            $this->client = $client;
        }
    }

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
