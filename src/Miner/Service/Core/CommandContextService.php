<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Core;

use Miner\Service\Auth\AuthService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandContextService
{
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
     * @param string $homeOptionName
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function registerHomeDir(string $homeOptionName, InputInterface $input, OutputInterface $output)
    {
        $homeDir = $input->getOption($homeOptionName);
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
    public function initializeUserContext()
    {
        try {
            $this->authService->getUser();
        } catch (\Exception $exception) {
            // silently ignore this error
        }
    }
}
