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
use Miner\Service\Renderer\ProjectRenderer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProjectContextInfoCommand
 */
class ProjectContextInfoCommand extends MinerCommand
{
    /**
     * @var ContextService
     */
    private $contextService;

    /**
     * @var \Miner\Service\Renderer\ProjectRenderer
     */
    private $projectRenderer;

    /**
     * ProjectContextInfoCommand constructor.
     *
     * @param \Miner\Service\Core\ContextService $contextService
     * @param \Miner\Service\Renderer\ProjectRenderer $projectRenderer
     */
    public function __construct(ContextService $contextService, ProjectRenderer $projectRenderer)
    {
        parent::__construct(null);
        $this->contextService = $contextService;
        $this->projectRenderer = $projectRenderer;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('project:context:info')
            ->setAliases(['pci'])
            ->setDescription(
                "Prints informations about the current project context."
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
        if (!$project) {
            $output->writeln("<comment>No project context informations found.</comment>");
            $output->writeln("Use 'project:context:set' to create your context.");
        } else {
            $this->projectRenderer->render($project, $output);
        }

        return 0;
    }
}
