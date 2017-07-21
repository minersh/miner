<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Model\Ticket;

use Miner\Factory\ProjectFactory;
use Miner\Factory\TicketFactory;
use Miner\Factory\UserFactory;
use Miner\Model\AbstractModel;
use Miner\Model\Project\Project;
use Miner\Model\User\User;

/**
 * Class Ticket
 */
class Ticket extends AbstractModel
{
    /**
     * @var TicketFactory
     */
    private $ticketFactory;

    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    /**
     * @var array
     */
    private $ticketData;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * Project constructor.
     *
     * @param TicketFactory $ticketFactory
     * @param ProjectFactory $projectFactory
     * @param UserFactory $userFactory
     * @param array $ticketData
     */
    public function __construct(
        TicketFactory $ticketFactory,
        ProjectFactory $projectFactory,
        UserFactory $userFactory,
        array $ticketData = []
    ) {
        $this->setModelData($ticketData);
        $this->ticketFactory = $ticketFactory;
        $this->projectFactory = $projectFactory;
        $this->userFactory = $userFactory;
    }

    /**
     * @param array $data
     */
    public function setModelData(array $data)
    {
        $this->ticketData = $data;
    }

    /**
     * @return array
     */
    public function getModelData()
    {
        return $this->ticketData;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->getIntegerOrNull('id');
    }

    /**
     * @return Project|null
     */
    public function getProject()
    {
        return isset($this->ticketData['project'])
            ? $this->projectFactory->createByProjectdata($this->ticketData['project'])
            : null;
    }

    /**
     * @return string|null
     */
    public function getTracker()
    {
        return $this->getStringOrNull('tracker', 'name');
    }

    /**
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getStringOrNull('status', 'name');
    }

    /**
     * @return string|null
     */
    public function getPriority()
    {
        return $this->getStringOrNull('priority', 'name');
    }

    /**
     * @return User|null
     */
    public function getAuthor()
    {
        return isset($this->ticketData['author'])
            ? $this->userFactory->createByUserdata($this->ticketData['author'])
            : null;
    }

    /**
     * @return User|null
     */
    public function getAssignedTo()
    {
        return isset($this->ticketData['assigned_to'])
            ? $this->userFactory->createByUserdata($this->ticketData['assigned_to'])
            : null;
    }

    /**
     * @return string|null
     */
    public function getSubject()
    {
        return $this->getStringOrNull('subject');
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getStringOrNull('description');
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate()
    {
        return $this->getDateTimeOrNull('start_date');
    }

    /**
     * @return \DateTime|null
     */
    public function getDueDate()
    {
        return $this->getDateTimeOrNull('due_date');
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedOn()
    {
        return $this->getDateTimeOrNull('created_on');
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedOn()
    {
        return $this->getDateTimeOrNull('updated_on');
    }

    /**
     * @return int|null
     */
    public function getDoneRatio()
    {
        return $this->getIntegerOrNull('done_ratio');
    }

    /**
     * @return float|null
     */
    public function getEstimatedHours()
    {
        return $this->getFloatOrNull('estimated_hours');
    }
}
