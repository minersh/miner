<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Core;

use Miner\Exceptions\EnvironmentException;

class EnvironmentService
{
    /**
     * @var string
     */
    private $homedirPreference;

    /**
     * @param string $homedirPreference
     *
     * @return $this
     */
    public function setHomedirPreference(string $homedirPreference)
    {
        $this->homedirPreference = $homedirPreference;
        return $this;
    }

    /**
     * @return string
     * @throws EnvironmentException
     */
    public function getHomedir()
    {
        if (empty($this->homedirPreference)) {
            throw EnvironmentException::missingHomedirConfiguration();
        }
        return $this->homedirPreference;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getFallbackHomeDir()
    {
        return trim(`cd && pwd`) . '/.miner';
    }

    /**
     * @return array|null
     */
    public function getUserData()
    {
        // TODO
        return null;
    }
}
