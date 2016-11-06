<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Core;

class EnvironmentService
{
    /**
     * @var SetupService
     */
    private $setupService;

    /**
     * AuthService constructor.
     *
     * @param SetupService $setupService
     */
    public function __construct(SetupService $setupService)
    {
        $this->setupService = $setupService;
    }

    /**
     * @return array|null
     */
    public function getUserData()
    {
        // TODO
        return null;
    }
}
