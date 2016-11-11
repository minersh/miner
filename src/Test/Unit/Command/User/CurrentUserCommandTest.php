<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Command\User;

use Miner\Command\User\CurrentUserCommand;
use Miner\Model\User\User;
use Miner\Service\Auth\AuthService;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CurrentUserCommandTest
 *
 * @covers \Miner\Command\User\CurrentUserCommand
 */
class CurrentUserCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $userMock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFirstname', 'getLastname', 'getLogin', 'getId'])
            ->getMock();

        $userMock
            ->expects($this->once())
            ->method('getFirstname')
            ->willReturn('getFirstname');

        $userMock
            ->expects($this->once())
            ->method('getLastname')
            ->willReturn('getLastname');

        $userMock
            ->expects($this->once())
            ->method('getLogin')
            ->willReturn('getLogin');

        $userMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn('getId');

        $authServiceMock = $this->getMockBuilder(AuthService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock();
        /* @var AuthService|Mock $authServiceMock */

        $authServiceMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userMock);

        $cmd = new CurrentUserCommand($authServiceMock);

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
