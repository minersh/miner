<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Exceptions;


class RegistryException extends \Exception
{
    /**
     * @param $key
     *
     * @return RegistryException
     */
    public static function elementNotFound(string $key)
    {
        return new static(
            sprintf(
                "The requested element '%s' can't be found.",
                $key
            )
        );
    }
}
