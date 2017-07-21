<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see LICENSE.txt
 */

namespace Miner\Service\Renderer;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TicketListRenderer
 */
class TicketListRenderer extends AbstractRenderer
{
    /**
     * @param \Miner\Model\Ticket\Ticket[] $tickets
     * @param int $currentUserId
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function render(array $tickets, int $currentUserId, $doTruncate, OutputInterface $output)
    {
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
            if (!$doTruncate) {
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
    }
}
