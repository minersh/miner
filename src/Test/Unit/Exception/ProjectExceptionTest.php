<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Test\Unit\Exception;

use Miner\Exceptions\ProjectException;

/**
 * Class ProjectExceptionTest
 *
 * @covers \Miner\Exceptions\ProjectException
 */
class ProjectExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testNoSuchProject()
    {
        $exception = ProjectException::noSuchProject();

        $this->assertInstanceOf(ProjectException::class, $exception);
        $this->assertEquals("The desired project can't be found.", $exception->getMessage());
    }
}
