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
use Miner\Service\Renderer\TicketListRenderer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TicketListCommand
 */
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
     * @var \Miner\Service\Renderer\TicketListRenderer
     */
    private $ticketListRenderer;

    /**
     * ProjectListCommand constructor.
     *
     * @param ContextService $contextService
     * @param RedmineApi $redmineApi
     * @param \Miner\Service\Renderer\TicketListRenderer $ticketListRenderer
     */
    public function __construct(
        ContextService $contextService,
        RedmineApi $redmineApi,
        TicketListRenderer $ticketListRenderer
    ) {
        parent::__construct(null);
        $this->contextService = $contextService;
        $this->redmineApi = $redmineApi;
        $this->ticketListRenderer = $ticketListRenderer;
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
        // get the current user context
        $contextUser = $this->contextService->getUser();
        $currentUserId = null;
        if ($contextUser) {
            $currentUserId = $contextUser->getId();
        }

        // get the user context for the list renadering
        $userId = $this->getUserContext($input, $currentUserId);

        // get the project context
        $projectId = $this->getProjectContext($input);

        // get the tickets
        $tickets = $this->redmineApi->getTicketApi()->getList($userId, $projectId);

        // render the list
        $this->ticketListRenderer->render(
            $tickets,
            $currentUserId,
            $input->getOption(self::OPT_NO_SUBJECT_TRUNCATE),
            $output
        );

        return 0;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param int|null $currentUserId
     *
     * @return int|null
     */
    private function getUserContext(InputInterface $input, int $currentUserId = null)
    {
        $userId = (int)$input->getOption(self::OPT_USER);
        if ($userId < 1) {
            if ($input->getOption(self::OPT_USER_IGNORE) || $input->getOption(self::OPT_ALL)) {
                $userId = null;
            } else {
                $userId = $currentUserId;
            }
        }

        return $userId;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return int|null
     */
    private function getProjectContext(InputInterface $input)
    {
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

        return $projectId;
    }
}
