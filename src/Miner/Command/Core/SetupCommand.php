<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command\Core;

use Miner\Command\MinerCommand;
use Miner\Service\Core\EnvironmentService;
use Miner\Service\Core\SetupService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends MinerCommand
{
    /**
     * @var SetupService
     */
    private $setupService;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * SetupCommand constructor.
     *
     * @param EnvironmentService $environmentService
     * @param SetupService $setupService
     */
    public function __construct(EnvironmentService $environmentService, SetupService $setupService)
    {
        parent::__construct();
        $this->environmentService = $environmentService;
        $this->setupService = $setupService;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('miner:setup');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $homedir = $this->environmentService->getHomedir();
        if (!file_exists($homedir)) {
            $this->setupService->installHomeDir($homedir);
        }

        return 0;
    }
}
