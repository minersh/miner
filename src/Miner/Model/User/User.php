<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Model\User;

use Miner\Model\AbstractModel;

/**
 * Class User
 */
class User extends AbstractModel
{
    /**
     * @var array
     */
    private $userdata;

    /**
     * User constructor.
     *
     * @param array $userdata
     */
    public function __construct(array $userdata = [])
    {
        $this->setModelData($userdata);
    }

    /**
     * @param array $data
     */
    public function setModelData(array $data)
    {
        $this->userdata = $data;
    }

    /**
     * @return array
     */
    public function getModelData()
    {
        return $this->userdata;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return isset($this->userdata['id'])
            ? (int)$this->userdata['id']
            : null;
    }

    /**
     * @return string|null
     */
    public function getLogin()
    {
        return isset($this->userdata['login'])
            ? (string)$this->userdata['login']
            : null;
    }

    /**
     * @return string|null
     */
    public function getFirstname()
    {
        return isset($this->userdata['firstname'])
            ? (string)$this->userdata['firstname']
            : null;
    }

    /**
     * @return string|null
     */
    public function getLastname()
    {
        return isset($this->userdata['lastname'])
            ? (string)$this->userdata['lastname']
            : null;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        if (isset($this->userdata['name'])) {
            return (string)$this->userdata['name'];
        }

        return trim(sprintf('%s %s', $this->getFirstname(), $this->getLastname()));
    }

    /**
     * @return string|null
     */
    public function getMail()
    {
        return isset($this->userdata['mail'])
            ? (string)$this->userdata['mail']
            : null;
    }

    /**
     * @return string|null
     */
    public function getApiKey()
    {
        return isset($this->userdata['api_key'])
            ? (string)$this->userdata['api_key']
            : null;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return isset($this->userdata['status'])
            ? (int)$this->userdata['status']
            : null;
    }
}
