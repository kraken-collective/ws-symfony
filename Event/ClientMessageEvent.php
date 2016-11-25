<?php

namespace KrakenCollective\WsSymfonyBundle\Event;

use Kraken\Network\NetworkMessageInterface;

class ClientMessageEvent extends ClientEvent
{
    /**
     * @var NetworkMessageInterface
     */
    protected $message;

    /**
     * @param NetworkMessageInterface $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return NetworkMessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }
}
