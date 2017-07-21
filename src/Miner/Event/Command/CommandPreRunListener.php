<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Event\Command;

use Miner\Api\EventListenerInterface;
use Miner\Command\MinerCommand;
use Miner\Service\Core\CommandContextService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class CommandPreRunListener implements EventListenerInterface
{
    const OPT_HOMEDIR = 'home';

    /**
     * @var CommandContextService
     */
    private $commandContextService;

    /**
     * CommandPreRunListener constructor.
     *
     * @param CommandContextService $commandContextService
     */
    public function __construct(CommandContextService $commandContextService)
    {
        $this->commandContextService = $commandContextService;
    }

    /**
     * @param Event $event
     *
     * @return void
     */
    public function handleEvent(Event $event)
    {
        if ($event instanceof ConsoleCommandEvent) {
            $this->prepareCommandOptions($event);
            $this->registerHomeDir($event->getInput(), $event->getOutput());

            $command = $event->getCommand();
            if ($command instanceof MinerCommand
                && $command->requiresAuthenticatedUser()
            ) {
                $this->initializeUserContext();
            }
        }
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    private function prepareCommandOptions(ConsoleCommandEvent $event)
    {
        if (!$event->getInput()->hasOption(self::OPT_HOMEDIR)) {
            $event
                ->getCommand()
                ->addOption(
                    self::OPT_HOMEDIR,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    "Defines the home directory to use for this miner installation"
                );
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function registerHomeDir(InputInterface $input, OutputInterface $output)
    {
        $this->commandContextService->registerHomeDir(self::OPT_HOMEDIR, $input, $output);
    }

    /**
     * @return void
     */
    private function initializeUserContext()
    {
        $this->commandContextService->initializeUserContext();
    }
}
