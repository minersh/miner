<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Model\Project;

use Miner\Factory\ProjectFactory;
use Miner\Model\AbstractModel;

/**
 * Class Project
 */
class Project extends AbstractModel
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
        $this->setModelData($projectData);
        $this->projectFactory = $projectFactory;
    }

    /**
     * @param array $data
     */
    public function setModelData(array $data)
    {
        $this->projectData = $data;
    }

    /**
     * @return array
     */
    public function getModelData()
    {
        return $this->projectData;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->getIntegerOrNull('id');
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->getStringOrNull('name');
    }

    /**
     * @return null|string
     */
    public function getIdentifier()
    {
        return $this->getStringOrNull('identifier');
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->getStringOrNull('description');
    }

    /**
     * @return int|null
     */
    public function getStatus()
    {
        return $this->getIntegerOrNull('status');
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->getBoolean('is_public');
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
        return $this->getDateTimeOrNull('created_on');
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedOn()
    {
        return $this->getDateTimeOrNull('updated_on');
    }
}
