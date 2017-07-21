<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see LICENSE.txt
 */

namespace Miner\Command\Ticket;

use Miner\Command\MinerCommand;
use Miner\Exceptions\TicketException;
use Miner\Service\Core\ContextService;
use Miner\Service\Redmine\RedmineApi;
use Miner\Service\Renderer\TicketRenderer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TicketDetailsCommand
 */
class TicketDetailsCommand extends MinerCommand
{
    const ARG_TICKET = 'ticket';
    const OPT_TITLE_ONLY = 'title-only';
    const OPT_DESCRIPTION_ONLY = 'description-only';

    /**
     * @var RedmineApi
     */
    private $redmineApi;

    /**
     * @var ContextService
     */
    private $contextService;

    /**
     * @var \Miner\Service\Renderer\TicketRenderer
     */
    private $ticketRenderer;

    /**
     * ProjectListCommand constructor.
     *
     * @param ContextService $contextService
     * @param RedmineApi $redmineApi
     * @param \Miner\Service\Renderer\TicketRenderer $ticketRenderer
     */
    public function __construct(ContextService $contextService, RedmineApi $redmineApi, TicketRenderer $ticketRenderer)
    {
        parent::__construct(null);
        $this->contextService = $contextService;
        $this->redmineApi = $redmineApi;
        $this->ticketRenderer = $ticketRenderer;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ticket:details')
            ->setAliases(['td', 't'])
            ->addArgument(
                self::ARG_TICKET,
                InputArgument::REQUIRED,
                'Number of the ticket to show.'
            )
            ->addOption(
                self::OPT_TITLE_ONLY,
                't',
                InputOption::VALUE_NONE,
                'Only return the title of the ticket as unformatted string.'
            )
            ->addOption(
                self::OPT_DESCRIPTION_ONLY,
                'd',
                InputOption::VALUE_NONE,
                'Only return the description of the ticket as unformatted string.'
            )
            ->setDescription(
                "Displays details about the provided ticket."
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Miner\Exceptions\TicketException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ticketApi = $this->redmineApi->getTicketApi();

        // valid id provided?
        $ticketId = $ticketApi->getNormalizedTicketId($input->getArgument(self::ARG_TICKET));
        if ($ticketId < 1) {
            throw TicketException::invalidTicketId($input->getArgument(self::ARG_TICKET));
        }

        // load ticket by id
        $ticket = $ticketApi->getTicket($ticketId);
        if (null === $ticket) {
            $output->writeln(
                "<comment>The ticket with the ID %d could not be found or you aren't allowed to access it.</comment>",
                $ticketId
            );

            return 1;
        }

        // only return the title of the ticket?
        if ($input->getOption(self::OPT_TITLE_ONLY)) {
            $output->write($ticket->getSubject());

            return 0;
        }

        // only return the description of the ticket?
        if ($input->getOption(self::OPT_DESCRIPTION_ONLY)) {
            $output->write($ticket->getDescription());

            return 0;
        }

        // render the ticket
        $this->ticketRenderer->render($ticket, $output);

        return 0;
    }
}
