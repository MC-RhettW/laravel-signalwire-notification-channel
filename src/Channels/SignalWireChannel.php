<?php

namespace MCDev\Notifications\Channels;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use MCDev\Notifications\Exceptions\SignalWireSendResultUnsuccessful as SendFailure;
use MCDev\Notifications\Messages\SignalWireMessage;
use SignalWire\Rest\Client;
use Twilio\Exceptions\TwilioException;

class SignalWireChannel
{
    /**
     * The SignalWire API client instance.
     *
     * @var Client
     */
    protected $api;

    /**
     * The phone number notifications should be sent from.
     *
     * @var string
     */
    protected $from;

    /**
     * Create a new SignalWire channel instance.
     *
     * @param  Client  $client
     * @param  string  $from
     * @return void
     */
    public function __construct(Client $client, string $from = '')
    {
        $this->from = empty($from) ? Config::get('signalwire.from') : $from;
        $this->api = $client;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return SignalWireMessage|null
     * @throws SendFailure
     */
    public function send($notifiable, Notification $notification): ?SignalWireMessage
    {
        // Determine destination or stop here if un-routable
        $to = $notifiable->routeNotificationFor('SignalWire', $notification)
            ?? $notifiable->routeNotificationFor('SMS', $notification);
        if (empty($to)) {
            return null;
        }

        // Create the text message for the notice
        $msgMethod = method_exists($notification, 'toSignalWire') ? 'toSignalWire' : 'toSms';
        $message = $notification->$msgMethod($notifiable);

        if (is_string($message)) {
            $message = new SignalWireMessage($message);
        }

        $payload = [
            //'context' => $message->context,
            'from' => $message->from ?: $this->from,
            'to' => $to,
            'body' => trim($message->content),
            //'tags' => $message->tags
        ];

        // Detect a number group being used as "From" element
        if (strlen($payload['from']) > 15) {
            $payload['MessagingServiceSid'] = $payload['from'];
            unset($payload['from']);
        }

        // Send the message and return the result / promise
        try {
            $result = ($message->client ?? $this->api)->messages->create($payload['to'],$payload);
        } catch (TwilioException $e) {
            throw new SendFailure($e->getMessage());
        }

        // Log the successful result according to config settings
        if (Config::get('signalwire.log.success_logging')) {
            Log::log(
                Config::get('signalwire.log.success_logging'),
                "Message {$result->sid} sent via SignalWire",
                Config::get('signalwire.log.include_context')
                    ? ['payload'=>$payload,'result'=>$result]
                    : []
            );
        }

        return $result;


    }
}
