<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Registry\User;

use Miner\Exceptions\RegistryException;
use Miner\Factory\UserFactory;
use Miner\Model\User\User;
use Miner\Registry\Registry;

class UserRegistry extends Registry
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * UserRegistry constructor.
     *
     * @param UserFactory $userFactory
     */
    public function __construct(UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    /**
     * @param array $userdata
     *
     * @return User
     * @throws RegistryException
     */
    public function getInstanceByData(array $userdata)
    {
        if (!$this->has($userdata['id'])) {
            $user = $this->userFactory->createByUserdata($userdata);
            $this->set($user->getId(), $user);
        } else {
            $user = $this->get($userdata['id']);
        }
        return $user;
    }
}
