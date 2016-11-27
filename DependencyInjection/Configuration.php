<?php

namespace KrakenCollective\WsSymfonyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kraken_collective_ws_symfony');

        $rootNode
            ->append($this->getServerNode())
            ->append($this->getSocketListenerNode())
        ;

        return $treeBuilder;
    }

    private function getServerNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('server');

        $node
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->info('The server name is going to be used to identify this server.')
            ->children()
                    ->scalarNode('listener')
                        ->isRequired()
                        ->info('Name of a defined socket_listener.')
                    ->end()
                    ->scalarNode('session_handler')
                        ->defaultValue('session.handler')
                        ->info('ID of a session handler. WARNING: Native session handlers will not work here. Use external provider, e.g. Redis.')
                    ->end()
                    ->arrayNode('authentication')
                        ->children()
                            ->arrayNode('firewalls')
                                ->info('Names of the firewalls that the User is going to be authenticated against.')
                                ->defaultValue(['default'])
                                ->requiresAtLeastOneElement()
                                ->prototype('scalar')->end()
                            ->end()
                            ->booleanNode('allow_anonymous')
                                ->defaultValue(false)
                                ->info('Switch to allow anonymous Users access.')
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('routes')
                        ->info('Public paths that allow connection to the server.')
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function getSocketListenerNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('socket_listener');

        $node
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->info('The socket_listener name is going to be used to identify this listener.')
            ->children()
                    ->scalarNode('protocol')
                        ->defaultValue('tcp')
                    ->end()
                    ->scalarNode('host')
                        ->isRequired()
                    ->end()
                    ->integerNode('port')
                        ->isRequired()
                    ->end()
                    ->enumNode('loop')
                        ->defaultValue('select_loop')
                        ->values(['select_loop'])
                        ->info('Event loop model that should be used for this listener. Currently only "select_loop" is available.')
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
