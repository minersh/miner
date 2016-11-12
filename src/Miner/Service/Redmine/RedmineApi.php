<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Redmine;

use Miner\Service\Redmine\Auth\RedmineAuthApi;
use Miner\Service\Redmine\Project\RedmineProjectApi;
use Miner\Service\Redmine\User\RedmineUserApi;
use Redmine\Client;

class RedmineApi
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var RedmineAuthApi
     */
    private $authApi;

    /**
     * @var RedmineUserApi
     */
    private $userApi;

    /**
     * @var RedmineProjectApi
     */
    private $redmineProjectApi;

    /**
     * RedmineApi constructor.
     *
     * @param RedmineAuthApi $authApi
     * @param RedmineUserApi $userApi
     * @param RedmineProjectApi $redmineProjectApi
     */
    public function __construct(
        RedmineAuthApi $authApi,
        RedmineUserApi $userApi,
        RedmineProjectApi $redmineProjectApi
    ) {
        $this->authApi = $authApi;
        $this->userApi = $userApi;
        $this->redmineProjectApi = $redmineProjectApi;
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        if (!$this->client) {
            if ($this->token) {
                $this->client = new Client($this->url, $this->token);
            } else {
                $this->client = new Client($this->url, $this->username, $this->password);
            }
        }
        return $this->client;
    }

    /**
     * @return $this
     */
    private function resetContext()
    {
        $this->client = null;
        $this->url = null;
        $this->token = null;
        $this->username = null;
        $this->password = null;

        return $this;
    }

    /**
     * @param string $url
     * @param string $token
     *
     * @return $this
     */
    public function createClientContextByToken(string $url, string $token)
    {
        $this->resetContext();

        $this->url = $url;
        $this->token = $token;

        return $this;
    }

    /**
     * @param string $url
     * @param string $username
     * @param string $password
     *
     * @return $this
     */
    public function createClientContextByCredentials(string $url, string $username, string $password)
    {
        $this->resetContext();

        $this->url = $url;
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * @return RedmineAuthApi
     */
    public function getAuthApi()
    {
        return $this->authApi->setClient($this, $this->getClient());
    }

    /**
     * @return RedmineUserApi
     */
    public function getUserApi()
    {
        return $this->userApi->setClient($this, $this->getClient());
    }

    /**
     * @return RedmineProjectApi
     */
    public function getProjectApi()
    {
        return $this->redmineProjectApi->setClient($this, $this->getClient());
    }
}
