<?php

namespace KrakenCollective\WsSymfonyBundle\Event;

use Error;
use Exception;

class ClientErrorEvent extends ClientEvent
{
    /**
     * @var Error|Exception
     */
    protected $ex;

    /**
     * @param Error|Exception
     */
    public function setThrowable($ex)
    {
        $this->ex = $ex;
    }

    /**
     * @return Error|Exception
     */
    public function getThrowable()
    {
        return $this->ex;
    }
}
