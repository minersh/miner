<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Redmine;

use Redmine\Client;

abstract class RedmineSubApi
{
    /**
     * @var RedmineApi
     */
    private $redmineApi;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param RedmineApi $redmineApi
     * @param Client $client
     *
     * @return $this
     */
    public function setClient(RedmineApi $redmineApi, Client $client)
    {
        $this->redmineApi = $redmineApi;
        $this->client = $client;
        return $this;
    }

    /**
     * @return RedmineApi
     */
    protected function getRedmineApi()
    {
        return $this->redmineApi;
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        return $this->client;
    }
}
