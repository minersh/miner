<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see LICENSE.txt
 */

namespace Miner\Service\Renderer;

use Miner\Model\Ticket\Ticket;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TicketRenderer
 */
class TicketRenderer extends AbstractRenderer
{
    /**
     * @param \Miner\Model\Ticket\Ticket $ticket
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function render(Ticket $ticket, OutputInterface $output)
    {
        $project = $ticket->getProject();
        $assignee = $ticket->getAssignedTo();

        $output->writeln(sprintf("ID: \t\t<info>%s</info>", $ticket->getId()));
        $output->writeln(sprintf("Subject: \t<info>%s</info>", $ticket->getSubject()));
        $output->writeln(sprintf("Assignee: \t<info>%s</info>", $assignee ? $assignee->getName() : '-'));
        $output->writeln(sprintf("Status: \t<info>%s</info>", $ticket->getStatus()));
        $output->writeln(sprintf("Priority: \t<info>%s</info>", $ticket->getPriority()));
        $output->writeln(sprintf("Project ID: \t<info>%s</info>", $project ? $project->getId() : '-'));
        $output->writeln(sprintf("Project title: \t<info>%s</info>", $project ? $project->getName() : '-'));

        $output->writeln(
            sprintf(
                "Ticket description:\n\n%s",
                $this->parseRedmineMarkup(
                    (string)($ticket->getDescription() ?: '-')
                )
            )
        );
    }
}
