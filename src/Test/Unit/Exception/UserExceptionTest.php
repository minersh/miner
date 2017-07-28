<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Test\Unit\Exception;

use Miner\Exceptions\UserException;

/**
 * Class UserExceptionTest
 *
 * @covers \Miner\Exceptions\UserException
 */
class UserExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testNoSuchUser()
    {
        $exception = UserException::noSuchUser();

        $this->assertInstanceOf(UserException::class, $exception);
        $this->assertEquals("The desired user can't be found.", $exception->getMessage());
    }
}
