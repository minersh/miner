<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command;

use Miner\Service\Core\EnvironmentService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class MinerCommand extends Command
{
    const OPT_HOMEDIR = 'home';

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * MinerCommand constructor.
     */
    public function __construct()
    {
        parent::__construct(null);

        /*
         * Add default options
         */
        $this->addOption(
            self::OPT_HOMEDIR,
            null,
            InputOption::VALUE_OPTIONAL,
            "Defines the home directory to use for this miner installation"
        );
    }

    /**
     * @param EnvironmentService $environmentService
     */
    public function setEnvironmentService(EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * @return string
     */
    protected function getHomeDir(): string
    {
        return $this->environmentService->getHomedir();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->registerHomeDir($input, $output);
        return 0;
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
}
