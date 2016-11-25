<?php
namespace KrakenCollective\WsSymfonyBundle\Exception;

use Exception;

class ServiceNotFoundException extends WsSymfonyException
{
    public function __construct($serviceAlias, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Service aliased "%s" does not exist.', $serviceAlias);

        parent::__construct($message, $code, $previous);
    }
}
