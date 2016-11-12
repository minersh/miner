<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Model\Project;


use Miner\Factory\ProjectFactory;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class Project
{
    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    /**
     * @var array
     */
    private $projectData;

    /**
     * Project constructor.
     *
     * @param ProjectFactory $projectFactory
     * @param array $projectData
     */
    public function __construct(ProjectFactory $projectFactory, array $projectData = [])
    {
        $this->setProjectData($projectData);
        $this->projectFactory = $projectFactory;
    }

    /**
     * @param array $projectData
     *
     * @return $this
     */
    public function setProjectData(array $projectData = [])
    {
        $this->projectData = $projectData;
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
                ['Identifier', $this->getIdentifier()],
                ['Name', $this->getName()],
                ['Description', $description],
                ['Parent', $this->getParent() ? $this->getParent()->getName() : '-'],
                ['Status', $this->getStatus()],
                ['Public', $this->isPublic() ? '<info>yes</info>' : '<comment>no</comment>'],
                ['Created On', $this->getCreatedOn()->format('Y-m-d H:i:s')],
                ['Updated On', $this->getUpdatedOn()->format('Y-m-d H:i:s')],
            ]
        );
        $table->render();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return isset($this->projectData['id'])
            ? (int)$this->projectData['id']
            : null;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return isset($this->projectData['name'])
            ? (string)$this->projectData['name']
            : null;
    }

    /**
     * @return null|string
     */
    public function getIdentifier()
    {
        return isset($this->projectData['identifier'])
            ? (string)$this->projectData['identifier']
            : null;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return isset($this->projectData['description'])
            ? (string)$this->projectData['description']
            : null;
    }

    /**
     * @return int|null
     */
    public function getStatus()
    {
        return isset($this->projectData['status'])
            ? (int)$this->projectData['status']
            : null;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return isset($this->projectData['is_public'])
            ? (bool)$this->projectData['is_public']
            : false;
    }

    /**
     * @return Project|null
     */
    public function getParent()
    {
        return isset($this->projectData['parent'])
            ? $this->projectFactory->createByProjectdata($this->projectData['parent'])
            : null;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedOn()
    {
        return isset($this->projectData['created_on'])
            ? new \DateTime($this->projectData['created_on'])
            : null;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedOn()
    {
        return isset($this->projectData['updated_on'])
            ? new \DateTime($this->projectData['updated_on'])
            : null;
    }
}
