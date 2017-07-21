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

/**
 * Class RedmineTicketApi
 */
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
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return Ticket[]
     */
    public function getList($userId = null, $projectId = null, $limit = null, $offset = null)
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

        $params = array_merge(
            [
                'limit' => is_null($limit) ? 10000 : (int)$limit,
                'offset' => is_null($offset) ? 0 : (int)$offset,
            ],
            $params
        );

        $data = $this->getClient()->issue->all($params);

        return $this->hydrate($data['issues']);
    }

    /**
     * @param int $ticketId
     *
     * @return Ticket|null
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
     * @param string $ticketId
     *
     * @return int
     */
    public function getNormalizedTicketId(string $ticketId)
    {
        return (int)str_replace('#', '', trim($ticketId));
    }

    /**
     * @param Ticket $ticket
     *
     * @return Ticket|null
     */
    public function save(Ticket $ticket)
    {
        if ($ticket->getId()) {
            $resp = $this->getClient()->issue->update($ticket->getId(), $ticket->getModelData());
        } else {
            $resp = $this->getClient()->issue->create($ticket->getModelData());
        }
        /* @var \SimpleXMLElement $resp */

        $ticketId = intval((string)$resp->id);
        if ($ticketId > 0) {
            return $this->getTicket($ticketId);
        }

        return null;
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
