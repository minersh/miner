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
use Miner\Service\Redmine\RedmineApi;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ProjectContextSetCommand extends MinerCommand
{
    const OPT_PROJECT = 'project';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var RedmineApi
     */
    private $redmineApi;

    /**
     * @var ContextService
     */
    private $contextService;

    /**
     * ProjectListCommand constructor.
     *
     * @param Application $application
     * @param ContextService $contextService
     * @param RedmineApi $redmineApi
     */
    public function __construct(Application $application, ContextService $contextService, RedmineApi $redmineApi)
    {
        parent::__construct(null);
        $this->application = $application;
        $this->contextService = $contextService;
        $this->redmineApi = $redmineApi;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('project:context:set')
            ->setAliases(['pcs'])
            ->addOption(
                self::OPT_PROJECT,
                'p',
                InputOption::VALUE_OPTIONAL,
                'ID of project to select.'
            )
            ->setDescription(
                "Set the project context for the current user."
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
        $projectId = (int)$input->getOption(self::OPT_PROJECT);
        if ($projectId > 0) {
            $returnCode = $this->registerContext($output, $projectId);
        } else {

            $this->runCommand(
                $this->application,
                $output,
                ProjectListCommand::CMD_NAME,
                ['--' . ProjectListCommand::OPT_DETAILS => false]
            );

            do {
                $helper = $this->getHelper('question');
                /* @var QuestionHelper $helper */

                $question = new Question('Select ID: ');
                $projectId = (int)$helper->ask($input, $output, $question);
            } while (1 > $projectId);

            $returnCode = $this->registerContext($output, $projectId);
        }

        return $returnCode;
    }

    /**
     * @param OutputInterface $output
     * @param int $projectId
     *
     * @return int
     */
    private function registerContext(OutputInterface $output, int $projectId)
    {
        $project = $this->redmineApi->getProjectApi()->getProject($projectId);
        if (!$project) {
            $output->writeln(
                "<error>Can't load project informations. Correct ID and project permissions?</error>"
            );
            return 1;
        }

        if (!$this->contextService->setProject($project)) {
            $output->writeln("<errpr>Can't save context.</errpr>");
            return 1;
        }

        $output->writeln(
            sprintf(
                'Context for project with ID <info>%s</info> successfully created.',
                $projectId
            )
        );
        return 0;
    }
}
