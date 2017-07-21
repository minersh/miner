<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Miner\Exceptions;

/**
 * Class UserException
 */
class UserException extends \Exception
{
    /**
     * @return UserException
     */
    public static function noSuchUser()
    {
        return new static("The desired user can't be found.");
    }
}
