<?php

namespace KrakenCollective\WsSymfonyBundle\Event;

final class Events
{
    const CLIENT_CONNECTED = 'kraken.ws.client_connected';
    const CLIENT_DISCONNECTED = 'kraken.ws.client_disconnected';
    const CLIENT_MESSAGE = 'kraken.ws.client_message';
    const CLIENT_ERROR = 'kraken.ws.client_error';
}
