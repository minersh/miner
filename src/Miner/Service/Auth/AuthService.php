<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Auth;

use Miner\Exceptions\AuthException;
use Miner\Model\User\User;
use Miner\Registry\User\UserRegistry;
use Miner\Service\Core\EnvironmentService;

class AuthService
{
    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * @var UserRegistry
     */
    private $userRegistry;

    /**
     * AuthService constructor.
     *
     * @param EnvironmentService $environmentService
     * @param UserRegistry $userRegistry
     */
    public function __construct(EnvironmentService $environmentService, UserRegistry $userRegistry)
    {
        $this->environmentService = $environmentService;
        $this->userRegistry = $userRegistry;
    }

    /**
     * @return User
     * @throws AuthException
     */
    public function getUser()
    {
        $userdata = $this->environmentService->getUserData();
        if (empty($userdata)) {
            throw AuthException::noUserConfigured();
        }

        return $this->userRegistry->getInstanceByData($userdata);
    }

    /**
     * @param string $apiToken
     *
     * @return bool
     */
    public function loginWithToken(string $apiToken)
    {
        // TODO
        return false;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function loginWithCredentials(string $username, string $password)
    {
        // TODO
        return false;
    }
}
