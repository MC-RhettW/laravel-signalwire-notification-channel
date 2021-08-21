<?php

namespace MCDev\Notifications\Messages;

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
     * The message type.
     *
     * @var string
     */
    public $type = 'text';

    /**
     * The custom SignalWire client instance.
     *
     * @var \SignalWire\Relay\Client|null
     */
    public $client;

    /**
     * Message tags
     *
     * @var array
     */
    public $tags = '';

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
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     * @return $this
     */
    public function content($content)
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
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the message type.
     *
     * @return $this
     */
    public function unicode()
    {
        $this->type = 'unicode';

        return $this;
    }

    /**
     * Set the client reference (up to 40 characters).
     *
     * @param  string  $clientReference
     * @return $this
     */
    public function withContext($context)
    {
        $this->context = $context ?? '';

        return $this;
    }

        /**
     * Set the client reference (up to 40 characters).
     *
     * @param  string|array  $tags
     * @return $this
     */
    public function withTags($tags)
    {
        if (!empty($tags)) {
            if (is_string($tags)) $this->tags = explode(',',$tags);
            elseif (!is_array($tags)) $this->tags = [$tags];
            else $this->tags = $tags;
        }
        else {
            $this->tags=[];
        }

        return $this;
    }

    /**
     * Set the SignalWire client instance.
     *
     * @param  \SignalWire\Client  $clientReference
     * @return $this
     */
    public function usingClient($client)
    {
        $this->client = $client;

        return $this;
    }
}
