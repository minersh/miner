<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Miner\Command\Ticket;

use Miner\Command\MinerCommand;
use Miner\Exceptions\ProjectException;
use Miner\Exceptions\TicketException;
use Miner\Exceptions\UserException;
use Miner\Factory\TicketFactory;
use Miner\Service\Core\ContextService;
use Miner\Service\Redmine\RedmineApi;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TicketCreateCommand
 */
class TicketCreateCommand extends MinerCommand
{
    const OPT_PROJECT = 'project';
    const OPT_USER = 'user';
    const OPT_USER_UNASSIGNED = 'no-user';

    const ARG_SUBJECT = 'subject';
    const OPT_DESCRIPTION = 'description';
    const OPT_RESPONSE_IDONLY = 'response-id-only';

    /**
     * @var RedmineApi
     */
    private $redmineApi;

    /**
     * @var ContextService
     */
    private $contextService;

    /**
     * @var TicketFactory
     */
    private $ticketFactory;

    /**
     * ProjectListCommand constructor.
     *
     * @param ContextService $contextService
     * @param RedmineApi $redmineApi
     * @param TicketFactory $ticketFactory
     */
    public function __construct(ContextService $contextService, RedmineApi $redmineApi, TicketFactory $ticketFactory)
    {
        parent::__construct(null);
        $this->contextService = $contextService;
        $this->redmineApi = $redmineApi;
        $this->ticketFactory = $ticketFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ticket:create')
            ->setAliases(['tc'])
            ->addOption(self::OPT_PROJECT, 'p', InputOption::VALUE_OPTIONAL, 'Project ID')
            ->addOption(self::OPT_USER, 'u', InputOption::VALUE_OPTIONAL, 'Assigned user ID')
            ->addOption(
                self::OPT_USER_UNASSIGNED,
                null,
                InputOption::VALUE_NONE,
                'Do not assign the ticket to any user.'
            )
            ->addOption(self::OPT_DESCRIPTION, 'd', InputOption::VALUE_OPTIONAL, 'Description')
            ->addOption(
                self::OPT_RESPONSE_IDONLY,
                null,
                InputOption::VALUE_NONE,
                'Only return the created ID instead of formatted output'
            )
            ->setDescription(
                "Creates a ticket within the specified project (or the current context if present)"
            );
        
        $this->addArgumentConfiguration();
    }

    /**
     * @return $this
     */
    protected function addArgumentConfiguration()
    {
        return $this
            ->addArgument(self::ARG_SUBJECT, InputArgument::REQUIRED, 'Subject of the ticket');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws ProjectException
     * @throws TicketException
     * @throws UserException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = (int)$input->getOption(self::OPT_PROJECT);
        if ($projectId < 1) {
            $contextProject = $this->contextService->getProject();
            if ($contextProject) {
                $projectId = $contextProject->getId();
            }
        }
        $project = $this->redmineApi
            ->getProjectApi()
            ->getProject($projectId);
        if (!$project) {
            throw ProjectException::noSuchProject();
        }

        $contextUser = $this->contextService->getUser();

        $userId = (int)$input->getOption(self::OPT_USER);
        if ($userId < 1) {
            $userId = null;
            if (!$input->getOption(self::OPT_USER_UNASSIGNED) && $contextUser) {
                $userId = $contextUser->getId();
            }
        }

        $user = false;
        if ($userId) {
            $user = $this->redmineApi
                ->getUserApi()
                ->getUserById($userId);
            if (!$user) {
                throw UserException::noSuchUser();
            }
        }

        $ticketData = [
            'subject' => $input->getArgument(self::ARG_SUBJECT),
            'description' => ($input->getOption(self::OPT_DESCRIPTION) ?: ''),
            'project_id' => $project->getId(),
        ];
        if ($user) {
            $ticketData['assigned_to_id'] = $user->getId();
        }

        $ticket = $this->redmineApi->getTicketApi()->save(
            $this->ticketFactory->createByTicketdata($ticketData)
        );

        if (!$ticket) {
            throw TicketException::creationFailed();
        }

        if ($input->getOption(self::OPT_RESPONSE_IDONLY)) {

            $output->writeln($ticket->getId());

        } else {
            $table = new Table($output);
            $table->setHeaders(
                [
                    'ID',
                    'Project',
                    'Subject',
                    'Assignee',
                ]
            );
            $table->addRow(
                [
                    $ticket->getId(),
                    $ticket->getProject()->getName(),
                    $ticket->getSubject(),
                    $ticket->getAssignedTo()->getName(),
                ]
            );
            $table->render();
        }

        return 0;
    }
}
