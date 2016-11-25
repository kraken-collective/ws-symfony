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
                ->children()
                    ->scalarNode('listener')->isRequired()->end()
                    ->scalarNode('session_handler')->defaultValue('session.handler')->end()
                    ->arrayNode('authentication')
                        ->children()
                            ->arrayNode('firewalls')
                                ->defaultValue(['default'])
                                ->requiresAtLeastOneElement()
                                ->prototype('scalar')->end()
                            ->end()
                            ->booleanNode('allow_anonymous')->defaultValue(false)->end()
                        ->end()
                    ->end()
                    ->arrayNode('routes')
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
                ->children()
                    ->scalarNode('protocol')->defaultValue('tcp')->end()
                    ->scalarNode('host')->isRequired()->end()
                    ->integerNode('port')->isRequired()->end()
                    ->scalarNode('loop')->defaultValue('select_loop')->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
