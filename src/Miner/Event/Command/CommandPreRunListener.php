<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Event\Command;

use Miner\Api\EventListenerInterface;
use Miner\Exceptions\AuthException;
use Miner\Service\Auth\AuthService;
use Miner\Service\Core\EnvironmentService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class CommandPreRunListener implements EventListenerInterface
{
    const OPT_HOMEDIR = 'home';

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * CommandPreRunListener constructor.
     *
     * @param EnvironmentService $environmentService
     * @param AuthService $authService
     */
    public function __construct(EnvironmentService $environmentService, AuthService $authService)
    {
        $this->environmentService = $environmentService;
        $this->authService = $authService;
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
            $this->initializeUserContext();
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
        $homeDir = $input->getOption(self::OPT_HOMEDIR);
        if (empty($homeDir)) {
            $homeDir = $this->environmentService->getFallbackHomeDir();
        }

        $output->writeln(
            sprintf(
                "[!] Using Home dir: <info>%s</info>",
                $homeDir
            ),
            OutputInterface::VERBOSITY_VERBOSE
        );

        $this->environmentService->setHomedirPreference($homeDir);
    }

    /**
     * @return void
     */
    private function initializeUserContext()
    {
        try {
            $this->authService->getUser();
        } catch (AuthException $exception) {
            // silently ignore this error
        }
    }
}
