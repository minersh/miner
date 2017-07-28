<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Test\Unit\Exception;

use Miner\Exceptions\TicketException;

/**
 * Class TicketExceptionTest
 *
 * @covers \Miner\Exceptions\TicketException
 */
class TicketExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreationFailed()
    {
        $exception = TicketException::creationFailed();

        $this->assertInstanceOf(TicketException::class, $exception);
        $this->assertEquals("Ticket creation failed. Please check provided informations.", $exception->getMessage());
    }

    public function testInvalidTicketId()
    {
        $ticketId = '123';
        $exception = TicketException::invalidTicketId($ticketId);

        $this->assertInstanceOf(TicketException::class, $exception);
        $this->assertEquals(sprintf("The provided ticket id %s is invalid.", $ticketId), $exception->getMessage());
    }
}
