<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Factory;

use Miner\Model\User\User;

class UserFactory
{
    /**
     * @return User
     */
    public function create()
    {
        return new User();
    }

    /**
     * @param array $userdata
     *
     * @return User
     */
    public function createByUserdata(array $userdata)
    {
        $user = $this->create();
        $user->setUserdata($userdata);
        return $user;
    }
}
