<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Miner\Exceptions;

/**
 * Class TicketException
 */
class TicketException extends \Exception
{
    /**
     * @return TicketException
     */
    public static function creationFailed()
    {
        return new static("Ticket creation failed. Please check provided informations.");
    }
}
