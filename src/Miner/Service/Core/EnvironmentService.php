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
    const FILE_USERDATA = 'userdata.json';

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
        $filename = $this->getHomedir() . '/' . self::FILE_USERDATA;
        if (file_exists($filename)) {
            $raw = file_get_contents($filename);
            if ($raw) {
                return json_decode($raw, true) ?: null;
            }
        }
        return null;
    }

    /**
     * @param string $realmurl
     * @param array $userdata
     *
     * @return bool
     */
    public function storeUserData(string $realmurl, array $userdata)
    {
        $data = [
            'realmurl' => $realmurl,
            'userdata' => $userdata,
        ];

        $filename = $this->getHomedir() . '/' . self::FILE_USERDATA;
        if (file_put_contents($filename, $this->encodeJson($data)) > 0) {
            chmod($filename, 0600);
            return true;
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function encodeJson($data)
    {
        return json_encode($data, (JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));
    }
}
