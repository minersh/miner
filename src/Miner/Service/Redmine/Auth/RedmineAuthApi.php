<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Redmine\Auth;

use Miner\Service\Redmine\RedmineSubApi;

class RedmineAuthApi extends RedmineSubApi
{
    /**
     * @return array|bool
     */
    public function login()
    {
        $userdata = $this->getRedmineApi()
            ->getUserApi()
            ->getCurrentUserData();
        return $userdata;
    }
}
