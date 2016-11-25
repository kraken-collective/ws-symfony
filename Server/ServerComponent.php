<?php

namespace KrakenCollective\WsSymfonyBundle\Server;

use Error;
use Exception;
use Kraken\Network\NetworkComponentInterface;
use Kraken\Network\NetworkConnectionInterface;
use Kraken\Network\NetworkMessageInterface;
use KrakenCollective\WsSymfonyBundle\Auth\AuthenticationProviderInterface;
use KrakenCollective\WsSymfonyBundle\Dispatcher\ClientEventDispatcher;
use KrakenCollective\WsSymfonyBundle\Event\ClientErrorEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientEvent;
use KrakenCollective\WsSymfonyBundle\Event\ClientMessageEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ServerComponent implements NetworkComponentInterface
{
    /** @var ClientEventDispatcher */
    protected $eventDispatcher;

    /** @var AuthenticationProviderInterface */
    private $authenticationProvider;

    /**
     * @param ClientEventDispatcher           $eventDispatcher
     * @param AuthenticationProviderInterface $authenticationProvider
     */
    public function __construct(
        ClientEventDispatcher $eventDispatcher,
        AuthenticationProviderInterface $authenticationProvider
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->authenticationProvider = $authenticationProvider;
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleConnect(NetworkConnectionInterface $conn)
    {
        if ($this->auth($conn)) {
            $event = new ClientEvent(ClientEvent::CONNECTED, $conn);
            $this->eventDispatcher->dispatchClientConnectEvent($event);
        }
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleDisconnect(NetworkConnectionInterface $conn)
    {
        if ($this->auth($conn)) {
            $event = new ClientEvent(ClientEvent::DISCONNECTED, $conn);
            $this->eventDispatcher->dispatchClientDisconnectEvent($event);
        }
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleMessage(NetworkConnectionInterface $conn, NetworkMessageInterface $message)
    {
        if ($this->auth($conn)) {
            $event = new ClientMessageEvent(ClientEvent::MESSAGE, $conn);
            $event->setMessage($message);
            $this->eventDispatcher->dispatchClientMessageEvent($event);
        }
    }

    /**
     * @override
     * @inheritDoc
     */
    public function handleError(NetworkConnectionInterface $conn, $ex)
    {
        $event = new ClientErrorEvent(ClientEvent::ERROR, $conn);
        $event->setThrowable($ex);
        $this->eventDispatcher->dispatchClientErrorEvent($event);
    }

    /**
     * @param NetworkConnectionInterface $conn
     * @return bool
     */
    private function auth(NetworkConnectionInterface $conn)
    {
        try {
            $this->authenticationProvider->auth($conn);
            return true;

        } catch (Error $ex) {
            $this->handleError($conn, $ex);

        } catch (Exception $ex) {
            $this->handleError($conn, $ex);
        }

        return false;
    }
}
