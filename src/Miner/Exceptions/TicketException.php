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

    /**
     * @param string $ticketId
     *
     * @return \Miner\Exceptions\TicketException
     */
    public static function invalidTicketId(string $ticketId)
    {
        return new static(sprintf("The provided ticket id %s is invalid.", $ticketId));
    }
}
