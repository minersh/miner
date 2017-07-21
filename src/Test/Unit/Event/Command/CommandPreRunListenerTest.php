<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Event\Command;

use Miner\Command\MinerCommand;
use Miner\Event\Command\CommandPreRunListener;
use Miner\Service\Core\CommandContextService;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandPreRunListenerTest
 *
 * @covers \Miner\Event\Command\CommandPreRunListener
 */
class CommandPreRunListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleEvent()
    {
        $inputMock = $this->getMockBuilder(InputInterface::class)
            ->getMock();

        $inputMock
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->equalTo(CommandPreRunListener::OPT_HOMEDIR))
            ->willReturn(false);

        $outputMock = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $contextMock = $this->getMockBuilder(CommandContextService::class)
            ->disableOriginalConstructor()
            ->setMethods(['registerHomeDir', 'initializeUserContext'])
            ->getMock();
        /* @var CommandContextService|Mock $contextMock */

        $contextMock
            ->expects($this->once())
            ->method('registerHomeDir')
            ->with(
                $this->equalTo(CommandPreRunListener::OPT_HOMEDIR),
                $this->equalTo($inputMock),
                $this->equalTo($outputMock)
            );

        $contextMock
            ->expects($this->once())
            ->method('initializeUserContext');

        $eventMock = $this->getMockBuilder(ConsoleCommandEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getInput', 'getOutput', 'getCommand'])
            ->getMock();
        /* @var ConsoleCommandEvent|Mock $eventMock */

        $eventMock
            ->expects($this->exactly(2))
            ->method('getInput')
            ->willReturn($inputMock);

        $eventMock
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn($outputMock);

        $cmdMock = $this->getMockBuilder(MinerCommand::class)
            ->disableOriginalConstructor()
            ->setMethods(['addOption', 'requiresAuthenticatedUser'])
            ->getMock();

        $cmdMock
            ->expects($this->once())
            ->method('addOption')
            ->with(
                $this->equalTo(CommandPreRunListener::OPT_HOMEDIR),
                $this->equalTo(null),
                $this->equalTo(InputOption::VALUE_OPTIONAL),
                $this->equalTo("Defines the home directory to use for this miner installation")
            );

        $cmdMock
            ->expects($this->once())
            ->method('requiresAuthenticatedUser')
            ->willReturn(true);

        $eventMock
            ->expects($this->exactly(2))
            ->method('getCommand')
            ->willReturn($cmdMock);

        $listener = new CommandPreRunListener($contextMock);
        $listener->handleEvent($eventMock);
    }
}
