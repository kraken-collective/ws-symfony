<?php
namespace KrakenCollective\WsSymfonyBundle\Helper;

use KrakenCollective\WsSymfonyBundle\Server\ServerConfig;

class ConnectionHelper
{
    /**
     * @param ServerConfig $serverConfig
     * @param string $token In most cases - session ID
     * @param string $route
     * @param string $protocol
     *
     * @return string
     */
    public function buildWebsocketAddress(ServerConfig $serverConfig, $token, $route = '/', $protocol = 'ws')
    {
        return sprintf(
            '%s://%s:%s%s?token=%s',
            $protocol,
            $serverConfig->getHost(),
            $serverConfig->getPort(),
            $route,
            $token
        );
    }
}
