<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Exception;

use Miner\Exceptions\EnvironmentException;

/**
 * Class EnvironmentExceptionTest
 *
 * @covers \Miner\Exceptions\EnvironmentException
 */
class EnvironmentExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $exception = EnvironmentException::missingHomedirConfiguration();

        $this->assertInstanceOf(EnvironmentException::class, $exception);
        $this->assertEquals("Internal Errlr: No homedir defined.", $exception->getMessage());
    }
}
