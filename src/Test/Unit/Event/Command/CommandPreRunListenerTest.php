<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Event\Command;

use Miner\Event\Command\CommandPreRunListener;
use Miner\Exceptions\AuthException;
use Miner\Service\Auth\AuthService;
use Miner\Service\Core\EnvironmentService;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Symfony\Component\Console\Command\Command;
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
    /**
     * @var CommandPreRunListener
     */
    private $listener;

    /**
     * @var EnvironmentService|Mock $environmentServiceMock
     */
    private $environmentServiceMock;

    /**
     * @var AuthService|Mock
     */
    private $authServiceMock;

    public function setUp()
    {
        $this->environmentServiceMock = $this->getMockBuilder(EnvironmentService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFallbackHomeDir', 'setHomedirPreference'])
            ->getMock();

        $this->authServiceMock = $this->getMockBuilder(AuthService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();

        $this->listener = new CommandPreRunListener(
            $this->environmentServiceMock,
            $this->authServiceMock
        );
    }

    public function test()
    {
        $homeDir = '/tmp/miner-test-home';

        $this->authServiceMock
            ->expects($this->once())
            ->method('getUser')
            ->willThrowException(AuthException::noUserConfigured());

        $this->environmentServiceMock
            ->expects($this->once())
            ->method('getFallbackHomeDir')
            ->willReturn($homeDir);

        $this->environmentServiceMock
            ->expects($this->once())
            ->method('setHomedirPreference')
            ->with($this->equalTo($homeDir));

        $inputMock = $this->getMockBuilder(InputInterface::class)
            ->getMock();

        $inputMock
            ->expects($this->once())
            ->method('hasOption')
            ->with($this->equalTo(CommandPreRunListener::OPT_HOMEDIR))
            ->willReturn(null);

        $inputMock
            ->expects($this->once())
            ->method('getOption')
            ->with($this->equalTo(CommandPreRunListener::OPT_HOMEDIR))
            ->willReturn(null);

        $outputMock = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $outputMock
            ->expects($this->once())
            ->method('writeln')
            ->with(
                $this->equalTo(
                    sprintf(
                        "[!] Using Home dir: <info>%s</info>",
                        $homeDir
                    )
                ),
                $this->equalTo(
                    OutputInterface::VERBOSITY_VERBOSE
                )
            );

        $commandMock = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->setMethods(['addOption'])
            ->getMock();

        $commandMock
            ->expects($this->once())
            ->method('addOption')
            ->with(
                $this->equalTo(CommandPreRunListener::OPT_HOMEDIR),
                $this->equalTo(null),
                $this->equalTo(InputOption::VALUE_OPTIONAL),
                $this->equalTo("Defines the home directory to use for this miner installation")
            );

        $eventMock = $this->getMockBuilder(ConsoleCommandEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCommand', 'getInput', 'getOutput'])
            ->getMock();
        /* @var ConsoleCommandEvent|Mock $eventMock */

        $eventMock
            ->expects($this->once())
            ->method('getCommand')
            ->willReturn($commandMock);

        $eventMock
            ->expects($this->exactly(2))
            ->method('getInput')
            ->willReturn($inputMock);

        $eventMock
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn($outputMock);

        $this->listener->handleEvent($eventMock);
    }
}
