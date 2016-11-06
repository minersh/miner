<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Registry;

use Miner\Exceptions\RegistryException;

class Registry
{
    /**
     * @var array
     */
    private $registry;

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->registry[$key]);
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     * @throws RegistryException
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw RegistryException::elementNotFound($key);
        }
        return $this->registry[$key];
    }

    /**
     * @param mixed $key
     * @param mixed $data
     *
     * @return $this
     */
    public function set($key, $data)
    {
        $this->registry[$key] = $data;
        return $this;
    }
}
