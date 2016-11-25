<?php

namespace KrakenCollective\WsSymfonyBundle\Auth;

use Kraken\Network\NetworkConnectionInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var array
     */
    protected $firewalls;

    /**
     * @var bool
     */
    protected $allowAnonymous;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param array $firewalls
     * @param bool $allowAnonymous
     */
    public function __construct(TokenStorageInterface $tokenStorage, array $firewalls = [], $allowAnonymous = false)
    {
        $this->tokenStorage = $tokenStorage;
        $this->firewalls = $firewalls;
        $this->allowAnonymous = $allowAnonymous;
        $this->hash = spl_object_hash($tokenStorage);
    }

    /**
     * @param NetworkConnectionInterface $conn
     * @return TokenInterface
     * @throws AccessDeniedException
     */
    public function auth(NetworkConnectionInterface $conn)
    {
        $token = null;

        if (isset($conn->Session) && $conn->Session) {
            foreach ($this->firewalls as $firewall) {
                if (false !== $serializedToken = $conn->Session->get('_security_' . $firewall, false)) {
                    $token = unserialize($serializedToken);
                    break;
                }
            }
        }

        if (null === $token && $this->allowAnonymous) {
            $token = new AnonymousToken($this->firewalls[0], 'anon-' . $conn->getResourceId());
        }

        if (null === $token) {
            throw new AccessDeniedException("Invalid credentials passed");
        }

        if ($this->tokenStorage->getToken() !== $token) {
            $this->tokenStorage->setToken($token);
        }

        return $token;
    }
}
