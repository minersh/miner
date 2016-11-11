<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Command\Core;

use Miner\Command\Core\SetupCommand;
use Miner\Service\Core\EnvironmentService;
use Miner\Service\Core\SetupService;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class SetupCommandTest
 *
 * @covers \Miner\Command\Core\SetupCommand
 */
class SetupCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function directoryProvider()
    {
        return [
            ['/tmp/test-home-dir-' . microtime(true)],
            [null],
        ];
    }

    /**
     * @param string|null $testHomeDir
     *
     * @dataProvider directoryProvider
     */
    public function test($testHomeDir)
    {
        $environmentServiceMock = $this->getMockBuilder(EnvironmentService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getHomedir'])
            ->getMock();
        /* @var Mock|EnvironmentService $environmentServiceMock */

        $setupServiceMock = $this->getMockBuilder(SetupService::class)
            ->disableOriginalConstructor()
            ->setMethods(['installHomeDir'])
            ->getMock();
        /* @var Mock|SetupService $setupServiceMock */

        $cmd = new SetupCommand($environmentServiceMock, $setupServiceMock);

        $fallbackDir = '/tmp/miner-fallback-dir';
        $expectedDir = $testHomeDir ?: $fallbackDir;

        $environmentServiceMock
            ->expects($this->any())
            ->method('getHomedir')
            ->willReturn($expectedDir);

        $inputMock = $this->getMockBuilder(InputInterface::class)
            ->getMock();

        $outputMock = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $ref = new \ReflectionClass($cmd);
        $method = $ref->getMethod('execute');
        $method->setAccessible(true);

        $exitCode = $method->invoke($cmd, $inputMock, $outputMock);

        $this->assertEquals(0, $exitCode);
    }
}
