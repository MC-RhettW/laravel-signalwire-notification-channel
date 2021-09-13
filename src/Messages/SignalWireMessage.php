<?php

namespace MCDev\Notifications\Messages;

use SignalWire\Rest\Client;

class SignalWireMessage
{
    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * The phone number the message should be sent from.
     *
     * @var string
     */
    public $from;

    /**
     * The custom SignalWire client instance.
     *
     * @var Client|null
     */
    public $client;

    /**
     * Message tags
     *
     * @var array
     */
    public $tags = [];

    /**
     * Message context
     *
     * @var string
     */
    public $context = 'notifications';

    /**
     * Create a new message instance.
     *
     * @param  string  $content
     * @return void
     */
    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     * @return $this
     */
    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the phone number the message should be sent from.
     *
     * @param  string  $from
     * @return $this
     */
    public function from(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the message type.
     *
     * @return $this
     */
    public function unicode(): self
    {
        $this->type = 'unicode';

        return $this;
    }

    /**
     * Set the SignalWire client instance.
     *
     * @param  Client  $client
     * @return $this
     */
    public function usingClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
