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
use Miner\Model\Project\Project;
use Miner\Model\User\User;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class Ticket
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
        $this->setTicketData($ticketData);
        $this->ticketFactory = $ticketFactory;
        $this->projectFactory = $projectFactory;
        $this->userFactory = $userFactory;
    }

    /**
     * @param array $ticketData
     *
     * @return $this
     */
    public function setTicketData(array $ticketData = [])
    {
        $this->ticketData = $ticketData;
        return $this;
    }

    /**
     * @param OutputInterface $output
     */
    public function render(OutputInterface $output)
    {
        $description = $this->getDescription();
        if (strlen($description) > 40) {
            $description = substr($description, 0, 37) . '...';
        }

        $table = new Table($output);
        $table->addRows(
            [
                ['ID', $this->getId()],
//                ['Identifier', $this->getIdentifier()],
//                ['Name', $this->getName()],
                ['Description', $description],
//                ['Parent', $this->getParent() ? $this->getParent()->getName() : '-'],
//                ['Status', $this->getStatus()],
//                ['Public', $this->isPublic() ? '<info>yes</info>' : '<comment>no</comment>'],
//                ['Created On', $this->getCreatedOn()->format('Y-m-d H:i:s')],
//                ['Updated On', $this->getUpdatedOn()->format('Y-m-d H:i:s')],
            ]
        );

        $table->render();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return isset($this->ticketData['id'])
            ? (int)$this->ticketData['id']
            : null;
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
     * @return mixed|null
     */
    public function getTracker()
    {
        return isset($this->ticketData['tracker']) && !empty($this->ticketData['tracker'])
            ? $this->ticketData['tracker']['name']
            : null;
    }

    /**
     * @return mixed|null
     */
    public function getStatus()
    {
        return isset($this->ticketData['status']) && !empty($this->ticketData['status'])
            ? $this->ticketData['status']['name']
            : null;
    }

    /**
     * @return mixed|null
     */
    public function getPriority()
    {
        return isset($this->ticketData['priority']) && !empty($this->ticketData['priority'])
            ? $this->ticketData['priority']['name']
            : null;
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
        return isset($this->ticketData['subject'])
            ? (string)$this->ticketData['subject']
            : null;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->ticketData['description'])
            ? (string)$this->ticketData['description']
            : null;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate()
    {
        return isset($this->ticketData['start_date'])
            ? new \DateTime($this->ticketData['start_date'])
            : null;
    }

    /**
     * @return \DateTime|null
     */
    public function getDueDate()
    {
        return isset($this->ticketData['due_date'])
            ? new \DateTime($this->ticketData['due_date'])
            : null;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedOn()
    {
        return isset($this->ticketData['created_on'])
            ? new \DateTime($this->ticketData['created_on'])
            : null;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedOn()
    {
        return isset($this->ticketData['updated_on'])
            ? new \DateTime($this->ticketData['updated_on'])
            : null;
    }

    /**
     * @return int|null
     */
    public function getDoneRatio()
    {
        return isset($this->ticketData['done_ratio'])
            ? (int)$this->ticketData['done_ratio']
            : null;
    }

    /**
     * @return float|null
     */
    public function getEstimatedHours()
    {
        return isset($this->ticketData['estimated_hours'])
            ? (float)$this->ticketData['estimated_hours']
            : null;
    }
}
