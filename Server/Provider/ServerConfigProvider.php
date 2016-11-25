<?php
namespace KrakenCollective\WsSymfonyBundle\Server\Provider;

use KrakenCollective\WsSymfonyBundle\Server\ServerConfig;

class ServerConfigProvider extends AbstractProvider
{
    /**
     * @param $alias
     *
     * @return object | ServerConfig
     */
    public function getServerConfig($alias)
    {
        return $this->get($alias);
    }
}
