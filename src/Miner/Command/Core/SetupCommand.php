<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command\Core;

use Miner\Command\MinerCommand;
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
     * SetupCommand constructor.
     *
     * @param SetupService $setupService
     */
    public function __construct(SetupService $setupService)
    {
        parent::__construct();
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
        parent::execute($input, $output);
        if (!file_exists($this->getHomeDir())) {
            $this->setupService->installHomeDir($this->getHomeDir());
        }

        return 0;
    }
}
