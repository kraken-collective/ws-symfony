<?php

namespace KrakenCollective\WsSymfonyBundle\DependencyInjection\Compiler;

use KrakenCollective\WsSymfonyBundle\DependencyInjection\KrakenCollectiveWsSymfonyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ServerProviderCompilerPass implements CompilerPassInterface
{
    const PARAMETER_SERVER_PROVIDER_ID = 'kraken.ws.server_provider';
    const PARAMETER_SERVER_CONFIG_PROVIDER_ID = 'kraken.ws.server_config_provider';
    const METHOD_REGISTER_SERVICE = 'registerService';

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $nonShared = [];
        $notFound = [];

        $serviceIds = $container->getServiceIds();

        foreach ($serviceIds as $serviceId) {
            if (!$container->hasDefinition($serviceId)) {
                $notFound[] = $serviceId;
                continue;
            }

            $def = $container->getDefinition($serviceId);
            if (!$def->isShared()) {
                $nonShared[] = $def;
            }
        }


        try {
            $serverProviderDefinition = $container->getDefinition(self::PARAMETER_SERVER_PROVIDER_ID);
            $serverConfigProviderDefinition = $container->getDefinition(self::PARAMETER_SERVER_CONFIG_PROVIDER_ID);

            $this->registerServicesInProvider(
                $container,
                $serverProviderDefinition,
                KrakenCollectiveWsSymfonyExtension::TAG_SERVER
            );
            $this->registerServicesInProvider(
                $container,
                $serverConfigProviderDefinition,
                KrakenCollectiveWsSymfonyExtension::TAG_SERVER_CONFIG
            );
        } catch (ServiceNotFoundException $e) {
            return;
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param Definition       $providerDefinition
     * @param string           $tag
     *
     * @return void
     */
    private function registerServicesInProvider(ContainerBuilder $container, Definition $providerDefinition, $tag)
    {
        $serviceIds = $container->findTaggedServiceIds($tag);

        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['alias'])) {
                    $providerDefinition->addMethodCall(
                        self::METHOD_REGISTER_SERVICE,
                        [$tag['alias'], $serviceId]
                    );
                }
            }
        }
    }
}
