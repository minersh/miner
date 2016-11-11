<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Exception;

use Miner\Exceptions\AuthException;

/**
 * Class AuthExceptionTest
 *
 * @covers \Miner\Exceptions\AuthException
 */
class AuthExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testElementNotFound()
    {
        $exception = AuthException::noUserConfigured();

        $this->assertInstanceOf(AuthException::class, $exception);
        $this->assertEquals("No User configured. Please login first!", $exception->getMessage());
    }
}
