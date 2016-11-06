<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Model\User;

class User
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
        $this->setUserdata($userdata);
    }

    /**
     * @param array $userdata
     *
     * @return $this
     */
    public function setUserdata(array $userdata)
    {
        $this->userdata = $userdata;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->userdata['id'];
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return (string)$this->userdata['login'];
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return (string)$this->userdata['firstname'];
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return (string)$this->userdata['lastname'];
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return (string)$this->userdata['mail'];
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return (string)$this->userdata['api_key'];
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return (int)$this->userdata['status'];
    }
}
