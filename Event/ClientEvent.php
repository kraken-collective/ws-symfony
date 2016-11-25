<?php

namespace KrakenCollective\WsSymfonyBundle\Event;

use Kraken\Network\NetworkConnectionInterface;
use Symfony\Component\EventDispatcher\Event;

class ClientEvent extends Event
{
    /**
     * @var int
     */
    const CONNECTED = 1;

    /**
     * @var int
     */
    const DISCONNECTED = 2;

    /**
     * @var int
     */
    const MESSAGE = 3;

    /**
     * @var int
     */
    const ERROR = 4;

    /**
     * @var NetworkConnectionInterface
     */
    protected $conn;

    /**
     * @var int
     */
    protected $type;

    /**
     * @param int $type
     * @param NetworkConnectionInterface $conn
     */
    public function __construct($type, NetworkConnectionInterface $conn)
    {
        $this->conn = $conn;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return NetworkConnectionInterface
     */
    public function getConnection()
    {
        return $this->conn;
    }
}
