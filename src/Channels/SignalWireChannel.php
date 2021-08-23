<?php

namespace MCDev\Notifications\Channels;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use MCDev\Notifications\Exceptions\SignalWireSendResultUnsuccessful as SendFailure;
use MCDev\Notifications\Messages\SignalWireMessage;
use SignalWire\Relay\Client;
use SignalWire\Relay\Messaging\SendResult;

class SignalWireChannel
{
    /**
     * The SignalWire API client instance.
     *
     * @var \SignalWire\Relay\Client
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
     * @param  \SignalWire\Relay\Client  $client
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
            'context' => $message->context,
            'from' => $message->from ?: $this->from,
            'to' => $to,
            'text' => trim($message->content),
            'tags' => $message->tags
        ];

        // Send the message and return the result / promise
        return ($message->client ?? $this->api)->messaging->send($payload)->done(function (SendResult $result) use (
            $payload
        ) {
            // Setup some handling vars
            $context = Config::get('signalwire.log_context', true) ? ['payload' => $payload, 'result' => $result] : [];
            $message_id = $result->getMessageId();
            $success_logging = strtolower(Config::get('signalwire.log.success_logging', 'info'));

            // Log any failed attempt and throw an exception
            if (!$result->isSuccessful()) {
                $exception = new SendFailure("SignalWire message '$message_id' was unsuccessful.");
                Log::error($exception->getMessage(), $context);
                throw $exception;
            } // Log successful attempts as appropriate
            elseif ($success_logging) {
                $logMethod = method_exists(Log::class, $success_logging) ? $success_logging : 'info';
                Log::$logMethod("SignalWire message '$message_id' sent successfully.", $context);
            }
        });
    }
}
