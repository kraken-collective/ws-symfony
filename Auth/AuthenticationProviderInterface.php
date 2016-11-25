<?php

namespace KrakenCollective\WsSymfonyBundle\Auth;

use Kraken\Network\NetworkConnectionInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface AuthenticationProviderInterface
{
    /**
     * @param NetworkConnectionInterface $conn
     * @return TokenInterface
     * @throws AccessDeniedException
     */
    public function auth(NetworkConnectionInterface $conn);
}
