<?php
namespace KrakenCollective\WsSymfonyBundle\Server;

class ServerConfig
{
    /** @var string */
    private $protocol;

    /** @var string */
    private $host;

    /** @var string */
    private $port;

    /**
     * @param string $protocol
     * @param string $host
     * @param string $port
     */
    public function __construct($protocol, $host, $port)
    {
        $this->protocol = $protocol;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }
}
