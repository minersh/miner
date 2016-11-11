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
use Miner\Service\Redmine\RedmineApi;

class AuthService
{
    /**
     * @var RedmineApi
     */
    private $redmineApi;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * @var UserRegistry
     */
    private $userRegistry;

    /**
     * @var array|null
     */
    private $currentUser;

    /**
     * AuthService constructor.
     *
     * @param RedmineApi $redmineApi
     * @param EnvironmentService $environmentService
     * @param UserRegistry $userRegistry
     */
    public function __construct(
        RedmineApi $redmineApi,
        EnvironmentService $environmentService,
        UserRegistry $userRegistry
    ) {
        $this->environmentService = $environmentService;
        $this->userRegistry = $userRegistry;
        $this->redmineApi = $redmineApi;
    }

    /**
     * @return User
     * @throws AuthException
     */
    public function getUser()
    {
        if (!$this->currentUser) {
            $this->currentUser = $this->environmentService->getUserData();
            if (empty($this->currentUser)) {
                throw AuthException::noUserConfigured();
            }
        }

        return $this->userRegistry->getInstanceByData($this->currentUser);
    }

    /**
     * @param string $realmurl
     * @param string $apiToken
     *
     * @return bool
     */
    public function loginWithToken(string $realmurl, string $apiToken)
    {
        $userdata = $this->redmineApi->createClientContextByToken($realmurl, $apiToken)
            ->getAuthApi()
            ->login();

        if (!$userdata || empty($userdata['user'])) {
            return false;
        }

        $this->environmentService->storeUserData($userdata['user']);

        return true;
    }

    /**
     * @param string $realmurl
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function loginWithCredentials(string $realmurl, string $username, string $password)
    {
        $userdata = $this->redmineApi->createClientContextByCredentials($realmurl, $username, $password)
            ->getAuthApi()
            ->login();

        if (!$userdata || !isset($userdata['user']) || !is_array($userdata['user'])) {
            return false;
        }

        $this->environmentService->storeUserData($userdata['user']);

        return true;
    }
}
