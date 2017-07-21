<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Redmine\User;

use Miner\Factory\UserFactory;
use Miner\Model\User\User;
use Miner\Service\Redmine\RedmineSubApi;

class RedmineUserApi extends RedmineSubApi
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * RedmineUserApi constructor.
     *
     * @param UserFactory $userFactory
     */
    public function __construct(UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    /**
     * @return array
     */
    public function getCurrentUserData()
    {
        $userdata = $this->getClient()->user->getCurrentUser();
        return $userdata;
    }

    /**
     * @param int $userId
     *
     * @return User|null
     */
    public function getUserById(int $userId)
    {
        $data = $this->getClient()->user->show($userId);
        if (!$data || !isset($data['user'])) {
            return null;
        }

        return $this->hydrate([$data['user']])[0];
    }

    /**
     * @param array $data
     *
     * @return User[]
     */
    private function hydrate(array $data)
    {
        $users = [];
        foreach ($data as $user) {
            $users[] = $this->userFactory->createByUserdata($user);
        }

        return $users;
    }
}
