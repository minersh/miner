<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Core;

use Miner\Exceptions\SetupException;

class SetupService
{
    /**
     * @param string $homeDir
     *
     * @return $this
     * @throws SetupException
     */
    public function installHomeDir(string $homeDir)
    {
        if (!file_exists($homeDir)) {
            if (!@mkdir($homeDir, 0700, true)) {
                throw SetupException::installationFailed(
                    sprintf(
                        "Can't create home directory %s",
                        escapeshellarg($homeDir)
                    )
                );
            }
        }
        return $this;
    }
}
