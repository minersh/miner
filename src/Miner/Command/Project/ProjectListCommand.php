<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command\Project;

use Miner\Command\MinerCommand;
use Miner\Model\Project\Project;
use Miner\Service\Redmine\RedmineApi;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectListCommand extends MinerCommand
{
    const CMD_NAME = 'project:list';

    const OPT_DETAILS = 'details';

    /**
     * @var RedmineApi
     */
    private $redmineApi;

    /**
     * ProjectListCommand constructor.
     *
     * @param RedmineApi $redmineApi
     */
    public function __construct(RedmineApi $redmineApi)
    {
        parent::__construct(null);
        $this->redmineApi = $redmineApi;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName(self::CMD_NAME)
            ->setAliases(['pl'])
            ->addOption(
                self::OPT_DETAILS,
                'd',
                InputOption::VALUE_NONE,
                "Display detailed oroject informations."
            )
            ->setDescription(
                "Lists all projects available for the currently logged in user."
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
        $projects = $this->redmineApi
            ->getProjectApi()
            ->getList();

        if (empty($projects)) {
            $output->writeln("<comment>No projects found for current user.</comment>");
        } else {
            $this->renderOutput($input, $output, $projects);
        }

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Project[] $projects
     */
    private function renderOutput(InputInterface $input, OutputInterface $output, array $projects)
    {
        $rows = [];
        if ($input->getOption(self::OPT_DETAILS)) {
            $headers = [
                'ID',
                'Identifier',
                'Project',
                'Parent',
                'Public',
            ];
            foreach ($projects as $project) {
                $rows[] = [
                    $project->getId(),
                    $project->getIdentifier(),
                    $project->getName(),
                    $project->getParent() ? $project->getParent()->getName() : '-',
                    $project->isPublic() ? '<info>yes</info>' : '<comment>no</comment>',
                ];
            }
        } else {
            $headers = [
                'ID',
                'Project',
            ];
            foreach ($projects as $project) {
                $rows[] = [$project->getId(), $project->getName()];
            }
        }

        $table = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($rows)
            ->render();
    }
}
