<?php

namespace KrakenCollective\WsSymfonyBundle;

use KrakenCollective\WsSymfonyBundle\DependencyInjection\Compiler\ServerProviderCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KrakenCollectiveWsSymfonyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ServerProviderCompilerPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
