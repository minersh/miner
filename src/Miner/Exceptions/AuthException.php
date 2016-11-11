<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Exceptions;


class AuthException extends \Exception
{
    /**
     * @return AuthException
     */
    public static function noUserConfigured()
    {
        return new static("No User configured. Please login first!");
    }
}
