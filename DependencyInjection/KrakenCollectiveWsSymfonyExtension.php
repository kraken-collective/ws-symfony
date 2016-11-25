<?php

namespace KrakenCollective\WsSymfonyBundle\DependencyInjection;

use ClassesWithParents\D;
use KrakenCollective\WsSymfonyBundle\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class KrakenCollectiveWsSymfonyExtension extends ConfigurableExtension implements CompilerPassInterface
{
    const CONFIG_SERVER = 'server';
    const CONFIG_SOCKET_LISTENER = 'socket_listener';

    const TAG_LOOP_MODEL = 'kraken.loop_model';
    const TAG_SERVER = 'kraken.server';
    const TAG_SERVER_CONFIG = 'kraken.server_config';

    const LOOP_CLASS_PARAM = 'kraken_collective.ws_symfony.loop';
    const SELECT_LOOP_CLASS_PARAM = 'kraken_collective.ws_symfony.select_loop';
    const SOCKET_LISTENER_CLASS_PARAM = 'kraken_collective.ws_symfony.socket_listener';
    const SESSION_PROVIDER_CLASS_PARAM = 'kraken_collective.ws_symfony.session_provider';
    const AUTHENTICATION_PROVIDER_CLASS_PARAM = 'kraken_collective.ws_symfony.authentication_provider';
    const SERVER_CLASS_PARAM = 'kraken_collective.ws_symfony.server';
    const SERVER_CONFIG_CLASS_PARAM = 'kraken_collective.ws_symfony.server_config';
    const NETWORK_SERVER_CLASS_PARAM = 'kraken_collective.ws_symfony.network_server';
    const WEBSOCKET_SERVER_CLASS_PARAM = 'kraken_collective.ws_symfony.websocket_server';
    const SERVER_COMPONENT_CLASS_PARAM = 'kraken_collective.ws_symfony.server_component';

    const ID_VENDOR_PREFIX = 'kraken.ws';

    const LOOP_ID_PREFIX = 'loop';
    const LOOP_MODEL_ID_PREFIX = 'loop_model';
    const SOCKET_LISTENER_ID_PREFIX = 'socket_listener';
    const SESSION_PROVIDER_ID_PREFIX = 'session_provider';
    const AUTHENTICATION_PROVIDER_ID_PREFIX = 'authentication_provider';
    const SERVER_ID_PREFIX = 'server';
    const SERVER_CONFIG_ID_PREFIX = 'server_config';
    const NETWORK_SERVER_ID_PREFIX = 'network_server';
    const WEBSOCKET_SERVER_ID_PREFIX = 'websocket_server';
    const SERVER_COMPONENT_ID_PREFIX = 'server_component';

    private $mergedConfig;

    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('parameters.yml');
        $loader->load('services.yml');

        $this->mergedConfig = $mergedConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->loadLoops($container);
        $this->loadSocketListeners($container, $this->mergedConfig[self::CONFIG_SOCKET_LISTENER]);
        $this->loadServers(
            $container,
            $this->mergedConfig[self::CONFIG_SERVER],
            $this->mergedConfig[self::CONFIG_SOCKET_LISTENER]
        );
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    private function loadLoops(ContainerBuilder $container)
    {
        $loopModelsIds = $container->findTaggedServiceIds(self::TAG_LOOP_MODEL);

        foreach ($loopModelsIds as $loopModelId => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['alias'])) {
                    $this->registerLoop(
                        $container,
                        $tag['alias'],
                        $loopModelId
                    );
                }
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $loopAlias
     * @param string           $loopModelId
     */
    private function registerLoop(
        ContainerBuilder $container,
        $loopAlias,
        $loopModelId
    ) {
        $definition = new Definition($container->getParameter(self::LOOP_CLASS_PARAM));
        $definition->addArgument(new Reference($loopModelId));

        $container->setDefinition(
            $this->getLoopServiceId($loopAlias),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $configs
     *
     * @return void
     */
    private function loadSocketListeners(ContainerBuilder $container, array $configs)
    {
        foreach ($configs as $socketListenerName => $config) {
            $this->registerSocketListener($container, $socketListenerName, $config);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $socketListenerName
     * @param array            $config
     *
     * @return void
     */
    private function registerSocketListener(ContainerBuilder $container, $socketListenerName, array $config)
    {
        $definition = new Definition($container->getParameter(self::SOCKET_LISTENER_CLASS_PARAM));

        $definition->addArgument(sprintf('%s://%s:%s', $config['protocol'], $config['host'], $config['port']));
        $definition->addArgument(new Reference($this->getLoopServiceId($config['loop'])));
        $definition->setPublic(false);

        $container->setDefinition(
            $this->getServiceId(self::SOCKET_LISTENER_ID_PREFIX, $socketListenerName),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $configs
     * @param array            $listenersConfig
     *
     * @return void
     */
    private function loadServers(ContainerBuilder $container, array $configs, array $listenersConfig)
    {
        foreach ($configs as $serverName => $config) {
            $this->loadServer($container, $serverName, $config, $listenersConfig);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $serverName
     * @param array            $config
     * @param array            $listenersConfig
     */
    private function loadServer(ContainerBuilder $container, $serverName, array $config, array $listenersConfig)
    {
        $this->registerAuthenticationProvider(
            $container,
            $config['authentication'],
            $serverName
        );

        $this->registerServerComponent(
            $container,
            $serverName
        );

        $this->registerSessionProvider(
            $container,
            $config['session_handler'],
            $serverName
        );

        $this->registerWebsocketServer(
            $container,
            $serverName
        );

        $this->registerNetworkServer(
            $container,
            $config['listener'],
            $config['routes'],
            $serverName
        );

        $this->registerServerConfig(
            $container,
            $listenersConfig[$config['listener']],
            $serverName
        );

        $this->registerServer(
            $container,
            $this->getLoopServiceIdFromSocketListener($container, $config['listener']),
            $config['listener'],
            $serverName
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     * @param string           $serverName
     */
    private function registerAuthenticationProvider(ContainerBuilder $container, array $config, $serverName)
    {
        $definition = new Definition($container->getParameter(self::AUTHENTICATION_PROVIDER_CLASS_PARAM));
        $definition->addArgument(new Reference('security.token_storage'));
        $definition->addArgument($config['firewalls']);
        $definition->addArgument($config['allow_anonymous']);
        $definition->setPublic(false);

        $container->setDefinition(
            $this->getServiceId(self::AUTHENTICATION_PROVIDER_ID_PREFIX, $serverName),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $serverName
     */
    private function registerServerComponent(
        ContainerBuilder $container,
        $serverName
    ) {
        $definition = new Definition($container->getParameter(self::SERVER_COMPONENT_CLASS_PARAM));
        $definition->addArgument(new Reference('kraken.ws.dispatcher.client_event'));
        $definition->addArgument(
            new Reference($this->getServiceId(self::AUTHENTICATION_PROVIDER_ID_PREFIX, $serverName))
        );
        $definition->setPublic(false);

        $container->setDefinition(
            $this->getServiceId(self::SERVER_COMPONENT_ID_PREFIX, $serverName),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $sessionHandlerId
     * @param string           $serverName
     */
    private function registerSessionProvider(
        ContainerBuilder $container,
        $sessionHandlerId,
        $serverName
    ) {
        $definition = new Definition($container->getParameter(self::SESSION_PROVIDER_CLASS_PARAM));
        $definition->addArgument(null);
        $definition->addArgument(new Reference($this->getServiceId(self::SERVER_COMPONENT_ID_PREFIX, $serverName)));
        $definition->addArgument(new Reference($sessionHandlerId));
        $definition->setPublic(false);

        $container->setDefinition(
            $this->getServiceId(self::SESSION_PROVIDER_ID_PREFIX, $serverName),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $serverName
     */
    private function registerWebsocketServer(
        ContainerBuilder $container,
        $serverName
    ) {
        $definition = new Definition($container->getParameter(self::WEBSOCKET_SERVER_CLASS_PARAM));
        $definition->addArgument(null);
        $definition->addArgument(new Reference($this->getServiceId(self::SESSION_PROVIDER_ID_PREFIX, $serverName)));

        $container->setDefinition(
            $this->getServiceId(self::WEBSOCKET_SERVER_ID_PREFIX, $serverName),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $listenerAlias
     * @param array            $routes
     * @param string           $serverName
     */
    private function registerNetworkServer(
        ContainerBuilder $container,
        $listenerAlias,
        array $routes,
        $serverName
    ) {
        $definition = new Definition($container->getParameter(self::NETWORK_SERVER_CLASS_PARAM));
        $definition->addArgument(new Reference($this->getServiceId(self::SOCKET_LISTENER_ID_PREFIX, $listenerAlias)));

        foreach ($routes as $route) {
            $definition->addMethodCall(
                'addRoute',
                [
                    $route,
                    new Reference($this->getServiceId(self::WEBSOCKET_SERVER_ID_PREFIX, $serverName))
                ]
            );
        }

        $container->setDefinition(
            $this->getServiceId(self::NETWORK_SERVER_ID_PREFIX, $serverName),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $listenerConfig
     * @param string           $serverName
     */
    private function registerServerConfig(ContainerBuilder $container, array $listenerConfig, $serverName)
    {
        $definition = new Definition($container->getParameter(self::SERVER_CONFIG_CLASS_PARAM));
        $definition->addArgument($listenerConfig['protocol']);
        $definition->addArgument($listenerConfig['host']);
        $definition->addArgument($listenerConfig['port']);
        $definition->addTag(self::TAG_SERVER_CONFIG, ['alias' => $serverName]);

        $container->setDefinition(
            $this->getServiceId(self::SERVER_CONFIG_ID_PREFIX, $serverName),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param Reference        $loopReference
     * @param string           $listenerAlias
     * @param string           $serverName
     */
    private function registerServer(
        ContainerBuilder $container,
        Reference $loopReference,
        $listenerAlias,
        $serverName
    ) {
        $definition = new Definition($container->getParameter(self::SERVER_CLASS_PARAM));
        $definition->addArgument($loopReference);
        $definition->addArgument(new Reference($this->getServiceId(self::SOCKET_LISTENER_ID_PREFIX, $listenerAlias)));
        $definition->addArgument(new Reference($this->getServiceId(self::NETWORK_SERVER_ID_PREFIX, $serverName)));
        $definition->addTag(self::TAG_SERVER, ['alias' => $serverName]);

        $container->setDefinition(
            $this->getServiceId(self::SERVER_ID_PREFIX, $serverName),
            $definition
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $socketListenerName
     * @return string
     */
    private function getLoopServiceIdFromSocketListener(ContainerBuilder $container, $socketListenerName)
    {
        $definition = $container->getDefinition(
            $this->getServiceId(self::SOCKET_LISTENER_ID_PREFIX, $socketListenerName)
        );

        return $definition->getArgument(1);
    }

    /**
     * @param string $loopAlias
     * @return string
     */
    private function getLoopServiceId($loopAlias)
    {
        return $this->getServiceId(self::LOOP_ID_PREFIX, $loopAlias);
    }

    /**
     * @param string $servicePrefix
     * @param string $serviceAlias
     * @return string
     */
    private function getServiceId($servicePrefix, $serviceAlias)
    {
        return sprintf('%s.%s', $this->getFullServicePrefix($servicePrefix), $serviceAlias);
    }

    /**
     * @param string $servicePrefix
     * @return string
     */
    private function getFullServicePrefix($servicePrefix)
    {
        return sprintf('%s.%s', self::ID_VENDOR_PREFIX, $servicePrefix);
    }
}
