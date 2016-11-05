<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Exception;

use Miner\Exceptions\SetupException;

/**
 * Class SetupExceptionTest
 *
 * @covers \Miner\Exceptions\SetupException
 */
class SetupExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstallationFailed()
    {
        $exception = SetupException::installationFailed('Test');

        $this->assertInstanceOf(SetupException::class, $exception);
        $this->assertEquals('Test', $exception->getMessage());
    }
}
