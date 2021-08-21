<?php

namespace MCDev\Notifications\Channels;

use MCDev\Notifications\Messages\SignalWireMessage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use MCDev\Notifications\Exceptions\SignalWireSendResultUnsuccessful as SendFailure;
use SignalWire\Relay\Client as Client;
use SignalWire\Relay\Messaging\SendResult;

class SignalWireChannel
{
    /**
     * The SignalWire API client instance.
     *
     * @var SignalWire\Relay\Client
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
     * @param  SignalWire\Relay\Client  $client
     * @param  string  $from
     * @return void
     */
    public function __construct(Client $client, $from='')
    {
        $this->from = empty($from) ? Config::get('signalwire.from') : $from;
        $this->api= $client;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return \SignalWire\Message\Message
     * @throws \MCDev\Notifications\Exceptions\SignalWireSendResultUnsuccessful
     */
    public function send($notifiable, Notification $notification)
    {
        // Determine destination or stop here if unroutable
        $to = $notifiable->routeNotificationFor('SignalWire', $notification) ?? $notifiable->routeNotificationFor('SMS', $notification);
        if (empty($to)) return;

        // Create the text message for the notice
        $msgMethod = method_exists($notification,'toSignalWire') ? 'toSignalWire' : 'toSms';
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
        return ($message->client ?? $this->api)->messaging->send($payload)->done(function (SendResult $result) use ($payload) {

            // Setup some handling vars
            $context = Config::get('signalwire.log_context',true) ? ['payload'=>$payload,'result'=>$result] : [];
            $message_id = $result->getMessageId();
            $success_logging = strtolower(Config::get('signalwire.log.success_logging','info'));

            // Log any failed attempt and throw an exception
            if (!$result->isSuccessful()) {
                $exception = new SendFailure("SignalWire message '$message_id' was unsuccessful.");
                Log::error($exception->getMessage(), $context);
                throw $exception;
            }

            // Log successful attempts as appropriate
            elseif ($success_logging) {
                $logMethod = method_exists(Log::class,$success_logging) ? $success_logging : 'info';
                Log::$logMethod("SignalWire message '$message_id' sent successfully.", $context);
            }

        });

    }
}
