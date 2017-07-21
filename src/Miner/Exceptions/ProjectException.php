<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Miner\Exceptions;

/**
 * Class ProjectException
 */
class ProjectException extends \Exception
{
    /**
     * @return ProjectException
     */
    public static function noSuchProject()
    {
        return new static("The desired project can't be found.");
    }
}
