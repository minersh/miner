<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command\Project;

use Miner\Command\MinerCommand;
use Miner\Service\Core\ContextService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectContextDeleteCommand extends MinerCommand
{
    /**
     * @var ContextService
     */
    private $contextService;

    /**
     * ProjectListCommand constructor.
     *
     * @param ContextService $contextService
     *
     * @internal param RedmineApi $redmineApi
     */
    public function __construct(ContextService $contextService)
    {
        parent::__construct(null);
        $this->contextService = $contextService;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('project:context:delete')
            ->setAliases(['pcd', 'pcr'])
            ->setDescription(
                "Delete the current project context."
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->contextService->getProject();
        if ($project) {
            $this->contextService->unsetProject();
            $output->writeln("<info>Project context removed.</info>");
        }

        return 0;
    }
}
