<?php
namespace KrakenCollective\WsSymfonyBundle\Server\Provider;

use KrakenCollective\WsSymfonyBundle\Server\Server;

class ServerProvider extends AbstractProvider
{
    /**
     * @param $alias
     *
     * @return object | Server
     */
    public function getServer($alias)
    {
        return $this->get($alias);
    }
}
