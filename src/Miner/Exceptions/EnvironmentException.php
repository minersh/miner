<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Exceptions;

class EnvironmentException extends \Exception
{
    /**
     * @return static
     */
    public static function missingHomedirConfiguration()
    {
        return new static("Internal Errlr: No homedir defined.");
    }
}
