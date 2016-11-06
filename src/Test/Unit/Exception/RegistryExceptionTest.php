<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Exception;

use Miner\Exceptions\RegistryException;

/**
 * Class RegistryExceptionTest
 *
 * @covers \Miner\Exceptions\RegistryException
 */
class RegistryExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testElementNotFound()
    {
        $key = 'test-key';

        $exception = RegistryException::elementNotFound($key);

        $this->assertInstanceOf(RegistryException::class, $exception);
        $this->assertEquals(
            sprintf(
                "The requested element '%s' can't be found.",
                $key
            ),
            $exception->getMessage()
        );
    }
}
