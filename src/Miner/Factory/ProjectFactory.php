<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Factory;

use Miner\Model\Project\Project;

class ProjectFactory
{
    /**
     * @return Project
     */
    public function create()
    {
        return new Project($this);
    }

    /**
     * @param array $projectdata
     *
     * @return Project
     */
    public function createByProjectdata(array $projectdata)
    {
        $instance = $this->create();
        $instance->setModelData($projectdata);
        return $instance;
    }
}
