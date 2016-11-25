<?php
namespace KrakenCollective\WsSymfonyBundle\Dispatcher;

use KrakenCollective\WsSymfonyBundle\Event\ClientEvent;
use KrakenCollective\WsSymfonyBundle\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientEventDispatcher
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ClientEvent $event
     *
     * @return void
     */
    public function dispatchClientConnectEvent(ClientEvent $event)
    {
        $this->eventDispatcher->dispatch(Events::CLIENT_CONNECTED, $event);
    }

    /**
     * @param ClientEvent $event
     *
     * @return void
     */
    public function dispatchClientDisconnectEvent(ClientEvent $event)
    {
        $this->eventDispatcher->dispatch(Events::CLIENT_DISCONNECTED, $event);
    }

    /**
     * @param ClientEvent $event
     *
     * @return void
     */
    public function dispatchClientMessageEvent(ClientEvent $event)
    {
        $this->eventDispatcher->dispatch(Events::CLIENT_MESSAGE, $event);
    }

    /**
     * @param ClientEvent $event
     *
     * @return void
     */
    public function dispatchClientErrorEvent(ClientEvent $event)
    {
        $this->eventDispatcher->dispatch(Events::CLIENT_ERROR, $event);
    }
}
