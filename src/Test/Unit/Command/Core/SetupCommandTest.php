<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Command\Core;

use Miner\Command\Core\SetupCommand;
use Miner\Command\MinerCommand;
use Miner\Service\Core\SetupService;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SetupCommandTest
 *
 * @covers \Miner\Command\Core\SetupCommand
 * @covers \Miner\Command\MinerCommand
 */
class SetupCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function directoryProvider()
    {
        return [
            ['/tmp/test-home-dir-' . microtime(true), true],
            [null, false]
        ];
    }

    /**
     * @param string|null $testHomeDir
     * @param bool $expectFullInstall
     *
     * @dataProvider directoryProvider
     */
    public function test($testHomeDir, $expectFullInstall)
    {
        $setupServiceMock = $this->getMockBuilder(SetupService::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        /* @var Mock|SetupService $setupServiceMock */

        $cmd = new SetupCommand($setupServiceMock);

        $expectedDir = $testHomeDir ?: $cmd->getFallbackHomeDir();

        $setupServiceMock
            ->expects($expectFullInstall ? $this->once() : $this->any())
            ->method('installHomeDir')
            ->with($this->equalTo($expectedDir));

        $inputMock = $this->getMockBuilder(InputInterface::class)
            ->getMock();

        $inputMock
            ->expects($this->once())
            ->method('getOption')
            ->with($this->equalTo(MinerCommand::OPT_HOMEDIR))
            ->willReturn($testHomeDir);

        $outputMock = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $outputMock
            ->expects($this->once())
            ->method('writeln')
            ->with(
                $this->equalTo(
                    sprintf(
                        "[!] Using Home dir: <info>%s</info>",
                        $expectedDir
                    )
                )
            );

        $ref = new \ReflectionClass($cmd);
        $method = $ref->getMethod('execute');
        $method->setAccessible(true);

        $exitCode = $method->invoke($cmd, $inputMock, $outputMock);

        $this->assertEquals(0, $exitCode);
        $this->assertAttributeEquals($expectedDir, 'homeDir', $cmd);
    }
}
