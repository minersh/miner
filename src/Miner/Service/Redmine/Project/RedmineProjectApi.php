<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Redmine\Project;

use Miner\Factory\ProjectFactory;
use Miner\Model\Project\Project;
use Miner\Service\Redmine\RedmineSubApi;

class RedmineProjectApi extends RedmineSubApi
{
    /**
     * @var ProjectFactory
     */
    private $projectFactory;

    /**
     * RedmineProjectApi constructor.
     *
     * @param ProjectFactory $projectFactory
     */
    public function __construct(ProjectFactory $projectFactory)
    {
        $this->projectFactory = $projectFactory;
    }

    /**
     * @return Project[]
     */
    public function getList()
    {
        $data = $this->getClient()->project->all();
        return $this->hydrate($data['projects']);
    }

    /**
     * @param int $projectId
     *
     * @return Project
     */
    public function getProject(int $projectId)
    {
        $data = $this->getClient()->project->show($projectId);
        if (!$data || !isset($data['project'])) {
            return null;
        }
        $projects = $this->hydrate(
            [
                $data['project'],
            ]
        );
        return current($projects);
    }

    /**
     * @param array $data
     *
     * @return Project[]
     */
    private function hydrate(array $data)
    {
        $projects = [];
        foreach ($data as $project) {
            $projects[] = $this->projectFactory->createByProjectdata($project);
        }
        return $projects;
    }
}
