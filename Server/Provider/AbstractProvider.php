<?php
namespace KrakenCollective\WsSymfonyBundle\Server\Provider;

use KrakenCollective\WsSymfonyBundle\Exception\RuntimeException;
use KrakenCollective\WsSymfonyBundle\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractProvider
{
    use ContainerAwareTrait;

    /** @var string[] */
    private $services = [];

    /**
     * @param string $alias
     *
     * @return object
     */
    protected function get($alias)
    {
        $this->guardAgainstMissingAlias($alias);

        return $this->returnService($alias);
    }

    /**
     * @param string $alias
     *
     * @return void
     *
     * @throws ServiceNotFoundException
     */
    private function guardAgainstMissingAlias($alias)
    {
        if (!isset($this->services[$alias])) {
            throw new ServiceNotFoundException($alias);
        }
    }

    /**
     * @param string $alias
     *
     * @return object
     */
    private function returnService($alias)
    {
        return $this->container->get(
            $this->services[$alias]
        );
    }

    /**
     * @param string $alias
     * @param string $serviceId
     *
     * @return void
     */
    public function registerService($alias, $serviceId)
    {
        $this->guardAgainstDuplicatedServiceAlias($alias);
        $this->addService($alias, $serviceId);
    }

    /**
     * @param string $alias
     *
     * @return void
     *
     * @throws RuntimeException
     */
    private function guardAgainstDuplicatedServiceAlias($alias)
    {
        if (isset($this->services[$alias])) {
            throw new RuntimeException(
                sprintf(
                    'Server aliased "%s" ("%") has already been registered. You cannot have multiple servers with the same alias!',
                    $alias,
                    $this->services[$alias]
                )
            );
        }
    }

    /**
     * @param string $alias
     * @param string $serviceId
     *
     * @return void
     */
    private function addService($alias, $serviceId)
    {
        $this->services[$alias] = $serviceId;
    }
}
