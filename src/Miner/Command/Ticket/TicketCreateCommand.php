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
use Miner\Service\Renderer\TicketRenderer;
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
     * @var \Miner\Service\Renderer\TicketRenderer
     */
    private $ticketRenderer;

    /**
     * ProjectListCommand constructor.
     *
     * @param ContextService $contextService
     * @param RedmineApi $redmineApi
     * @param TicketFactory $ticketFactory
     * @param \Miner\Service\Renderer\TicketRenderer $ticketRenderer
     */
    public function __construct(
        ContextService $contextService,
        RedmineApi $redmineApi,
        TicketFactory $ticketFactory,
        TicketRenderer $ticketRenderer
    ) {
        parent::__construct(null);
        $this->contextService = $contextService;
        $this->redmineApi = $redmineApi;
        $this->ticketFactory = $ticketFactory;
        $this->ticketRenderer = $ticketRenderer;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ticket:create')
            ->setAliases(['tc'])
            ->addOption(
                self::OPT_PROJECT,
                'p',
                InputOption::VALUE_OPTIONAL,
                'Project ID'
            )
            ->addOption(
                self::OPT_USER,
                'u',
                InputOption::VALUE_OPTIONAL,
                'Assigned user ID'
            )
            ->addOption(
                self::OPT_USER_UNASSIGNED,
                null,
                InputOption::VALUE_NONE,
                'Do not assign the ticket to any user.'
            )
            ->addOption(
                self::OPT_DESCRIPTION,
                'd',
                InputOption::VALUE_OPTIONAL,
                'Description'
            )
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
        // get the project
        $project = $this->getProjectForTicket($input);

        // collect ticket informations
        $ticketData = [
            'subject' => (string)$input->getArgument(self::ARG_SUBJECT),
            'description' => (string)$input->getOption(self::OPT_DESCRIPTION),
            'project_id' => (int)$project->getId(),
        ];

        // assign a user?
        $user = $this->getUserForTicket($input);
        if ($user) {
            $ticketData['assigned_to_id'] = $user->getId();
        }

        // create the ticket
        $ticket = $this->createTicket($ticketData);

        // return just the ID or render the whole ticket?
        if ($input->getOption(self::OPT_RESPONSE_IDONLY)) {
            $output->writeln($ticket->getId());
        } else {
            $this->ticketRenderer->render($ticket, $output);
        }

        return 0;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \Miner\Model\Project\Project
     * @throws \Miner\Exceptions\ProjectException
     */
    private function getProjectForTicket(InputInterface $input)
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

        return $project;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return bool|\Miner\Model\User\User|null
     * @throws \Miner\Exceptions\UserException
     */
    private function getUserForTicket(InputInterface $input)
    {
        $contextUser = $this->contextService->getUser();

        $userId = (int)$input->getOption(self::OPT_USER);
        if ($userId < 1) {
            $userId = null;
            if (!$input->getOption(self::OPT_USER_UNASSIGNED) && $contextUser) {
                $userId = (int)$contextUser->getId();
            }
        }

        $user = $this->loadUser($userId);

        return $user;
    }

    /**
     * @param int $userId
     *
     * @return bool|\Miner\Model\User\User|null
     * @throws \Miner\Exceptions\UserException
     */
    private function loadUser(int $userId)
    {
        $user = false;
        if ($userId) {
            $user = $this->redmineApi
                ->getUserApi()
                ->getUserById($userId);
            if (!$user) {
                throw UserException::noSuchUser();
            }
        }

        return $user;
    }

    /**
     * @param array $ticketData
     *
     * @return \Miner\Model\Ticket\Ticket|null
     * @throws \Miner\Exceptions\TicketException
     */
    private function createTicket(array $ticketData)
    {
        $ticket = $this->redmineApi->getTicketApi()->save(
            $this->ticketFactory->createByTicketdata($ticketData)
        );

        if (!$ticket) {
            throw TicketException::creationFailed();
        }

        return $ticket;
    }
}
