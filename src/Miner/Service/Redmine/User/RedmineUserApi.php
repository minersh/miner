<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Redmine\User;

use Miner\Service\Redmine\RedmineSubApi;

class RedmineUserApi extends RedmineSubApi
{
    /**
     * @return array
     */
    public function getCurrentUserData()
    {
        $userdata = $this->getClient()->user->getCurrentUser();
        return $userdata;
    }
}
