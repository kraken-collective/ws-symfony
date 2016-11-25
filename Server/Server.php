<?php
namespace KrakenCollective\WsSymfonyBundle\Server;

use Kraken\Ipc\Socket\SocketListenerInterface;
use Kraken\Loop\LoopExtendedInterface;
use Kraken\Network\NetworkServerInterface;

/**
 * This class aggregates the vital components of the Kraken Websocket library.
 * It should be used for convenience reasons - it contains the most important information and methods
 * that are needed to start the Websocket server.
 */
class Server
{
    /** @var LoopExtendedInterface */
    private $loop;

    /** @var SocketListenerInterface */
    private $socketListener;

    /** @var NetworkServerInterface */
    private $networkServer;

    /**
     * @param LoopExtendedInterface   $loop
     * @param SocketListenerInterface $socketListener
     * @param NetworkServerInterface  $networkServer
     */
    public function __construct(
        LoopExtendedInterface $loop,
        SocketListenerInterface $socketListener,
        NetworkServerInterface $networkServer
    ) {
        $this->loop = $loop;
        $this->networkServer = $networkServer;
        $this->socketListener = $socketListener;
    }

    /**
     * This function starts the event loop. Once it's called, the server is ready to receive messages.
     * This function is blocking.
     */
    public function start()
    {
        $this->loop->start();
    }

    /**
     * Stops the event loop, effectively disabling the server.
     */
    public function stop()
    {
        $this->loop->stop();
    }

    /**
     * @return string
     */
    public function getLocalAddress()
    {
        return $this->socketListener->getLocalAddress();
    }

    /**
     * @return string
     */
    public function getLocalHost()
    {
        return $this->socketListener->getLocalHost();
    }

    /**
     * @return string
     */
    public function getLocalPort()
    {
        return $this->socketListener->getLocalPort();
    }

    /**
     * @return LoopExtendedInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * @return NetworkServerInterface
     */
    public function getNetworkServer()
    {
        return $this->networkServer;
    }
}
