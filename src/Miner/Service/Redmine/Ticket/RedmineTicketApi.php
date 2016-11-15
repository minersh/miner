<?php
/**
 * This file is part of the miner ticket.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Redmine\Ticket;


use Miner\Factory\TicketFactory;
use Miner\Model\Ticket\Ticket;
use Miner\Service\Redmine\RedmineSubApi;

class RedmineTicketApi extends RedmineSubApi
{
    /**
     * @var TicketFactory
     */
    private $ticketFactory;

    /**
     * RedmineTicketApi constructor.
     *
     * @param TicketFactory $ticketFactory
     */
    public function __construct(TicketFactory $ticketFactory)
    {
        $this->ticketFactory = $ticketFactory;
    }

    /**
     * @param int|null $userId
     * @param int|null $projectId
     *
     * @return \Miner\Model\Ticket\Ticket[]
     */
    public function getList($userId = null, $projectId = null)
    {
        $userId = (int)$userId;
        $projectId = (int)$projectId;

        $params = [];
        if ($userId > 0) {
            $params['assigned_to_id'] = $userId;
        }
        if ($projectId > 0) {
            $params['project_id'] = $projectId;
        }

        $data = $this->getClient()->issue->all($params);

        return $this->hydrate($data['issues']);
    }

    /**
     * @param int $ticketId
     *
     * @return Ticket
     */
    public function getTicket(int $ticketId)
    {
        $data = $this->getClient()->issue->show($ticketId);
        if (!$data || !isset($data['issue'])) {
            return null;
        }
        $tickets = $this->hydrate(
            [
                $data['issue'],
            ]
        );
        return current($tickets);
    }

    /**
     * @param array $data
     *
     * @return Ticket[]
     */
    private function hydrate(array $data)
    {
        $tickets = [];
        foreach ($data as $ticket) {
            $tickets[] = $this->ticketFactory->createByTicketdata($ticket);
        }
        return $tickets;
    }
}
