<?php
namespace KrakenCollective\WsSymfonyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('kraken:ws:start')
            ->addArgument('server', InputArgument::REQUIRED, 'Name of the server to run.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serverName     = $input->getArgument('server');
        $serverProvider = $this->getContainer()->get('kraken.ws.server_provider');
        $server         = $serverProvider->getServer($serverName);

        $server->start();
    }
}
