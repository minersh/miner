<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Factory;

use Miner\Model\Ticket\Ticket;

class TicketFactory
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    /**
     * TicketFactory constructor.
     *
     * @param UserFactory $userFactory
     * @param ProjectFactory $projectFactory
     */
    public function __construct(UserFactory $userFactory, ProjectFactory $projectFactory)
    {
        $this->projectFactory = $projectFactory;
        $this->userFactory = $userFactory;
    }

    /**
     * @return Ticket
     */
    public function create()
    {
        return new Ticket($this, $this->projectFactory, $this->userFactory);
    }

    /**
     * @param array $ticketdata
     *
     * @return Ticket
     */
    public function createByTicketdata(array $ticketdata)
    {
        $instance = $this->create();
        $instance->setTicketData($ticketdata);
        return $instance;
    }
}
