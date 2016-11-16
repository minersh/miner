<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command\Ticket;

use Miner\Command\MinerCommand;
use Miner\Service\Core\ContextService;
use Miner\Service\Redmine\RedmineApi;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TicketListCommand extends MinerCommand
{
    const OPT_PROJECT = 'project';
    const OPT_PROJECT_IGNORE = 'ignore-project';
    const OPT_USER = 'user';
    const OPT_USER_IGNORE = 'ignore-user';
    const OPT_NO_SUBJECT_TRUNCATE = 'no-truncate';
    const OPT_ALL = 'all';

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
     * @param ContextService $contextService
     * @param RedmineApi $redmineApi
     */
    public function __construct(ContextService $contextService, RedmineApi $redmineApi)
    {
        parent::__construct(null);
        $this->contextService = $contextService;
        $this->redmineApi = $redmineApi;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ticket:list')
            ->setAliases(['tl'])
            ->addOption(
                self::OPT_ALL,
                'a',
                InputOption::VALUE_NONE,
                'Return all tickets, ignore user and project context.'
            )
            ->addOption(
                self::OPT_USER,
                'u',
                InputOption::VALUE_OPTIONAL,
                'ID of user to filter for.'
            )
            ->addOption(
                self::OPT_USER_IGNORE,
                null,
                InputOption::VALUE_NONE,
                'Ignore user context.'
            )
            ->addOption(
                self::OPT_PROJECT,
                'p',
                InputOption::VALUE_OPTIONAL,
                'ID of project to filter for.'
            )
            ->addOption(
                self::OPT_PROJECT_IGNORE,
                null,
                InputOption::VALUE_NONE,
                'Ignore project context.'
            )
            ->addOption(
                self::OPT_NO_SUBJECT_TRUNCATE,
                't',
                InputOption::VALUE_NONE,
                'Do not truncate sicket subject.'
            )
            ->setDescription(
                "Returns the list of all relevant tickets the user has access for."
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
        $currentUserId = null;
        $contextUser = $this->contextService->getUser();
        if ($contextUser) {
            $currentUserId = $contextUser->getId();
        }

        $userId = (int)$input->getOption(self::OPT_USER);
        if ($userId < 1) {
            if ($input->getOption(self::OPT_USER_IGNORE) || $input->getOption(self::OPT_ALL)) {
                $userId = null;
            } else {
                $userId = $currentUserId;
            }
        }

        $projectId = (int)$input->getOption(self::OPT_PROJECT);
        if ($projectId < 1) {
            if ($input->getOption(self::OPT_PROJECT_IGNORE) || $input->getOption(self::OPT_ALL)) {
                $projectId = null;
            } else {
                $contextProject = $this->contextService->getProject();
                if ($contextProject) {
                    $projectId = $contextProject->getId();
                }
            }
        }

        $tickets = $this->redmineApi->getTicketApi()->getList($userId, $projectId);

        $table = new Table($output);
        $table->setHeaders(
            [
                'ID',
                'Ticket',
                'Assignee',
                'Status',
                'Priority',
                'Project',
                'Project ID',
            ]
        );
        foreach ($tickets as $ticket) {
            $project = $ticket->getProject();
            $assignedUser = $ticket->getAssignedTo();

            if ($assignedUser) {
                if ($assignedUser->getId() == $currentUserId) {
                    $assignedUserName = '<comment>' . $assignedUser->getName() . '</comment>';
                } else {
                    $assignedUserName = $assignedUser->getName();
                }
            } else {
                $assignedUserName = '-';
            }

            $subject = $ticket->getSubject();
            if (!$input->getOption(self::OPT_NO_SUBJECT_TRUNCATE)) {
                if (strlen($subject) > 30) {
                    $subject = substr($subject, 0, 27) . '...';
                }
            }

            $table->addRow(
                [
                    $ticket->getId(),
                    $subject,
                    $assignedUserName,
                    $ticket->getStatus(),
                    $ticket->getPriority(),
                    $project->getName(),
                    $project->getId(),
                ]
            );
        }
        $table->render();

        return 0;
    }
}
