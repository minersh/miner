<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Exceptions;

class SetupException extends \Exception
{
    /**
     * @param string $msg
     *
     * @return SetupException
     */
    public static function installationFailed(string $msg)
    {
        return new self($msg);
    }
}
