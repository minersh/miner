<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Command\Auth;

use Miner\Command\Auth\LoginCommand;
use Miner\Service\Auth\AuthService;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LoginCommandTest
 *
 * @covers \Miner\Command\Auth\LoginCommand
 */
class LoginCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoginCommand
     */
    private $command;

    /**
     * @var Mock|AuthService
     */
    private $authServiceMock;

    public function setUp()
    {
        $this->authServiceMock = $this->getMockBuilder(AuthService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->command = new LoginCommand($this->authServiceMock);
    }

    public function executeDataProvider()
    {
        return [
            ['', '', '', false],
            ['token', '', '', true],
            ['', 'user', 'pass', true],
            ['', 'user', '', true],
            ['token', 'user', '', true],
            ['token', 'user', 'pass', false]
        ];
    }

    /**
     * @param string|null $apitoken
     * @param string|null $username
     * @param string|null $password
     * @param bool $loginSuccess
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute($apitoken, $username, $password, $loginSuccess)
    {
        $exptectedReturnCode = 0;

        $inputMock = $this->getMockBuilder(InputInterface::class)
            ->getMock();

        $inputMock
            ->expects($this->exactly(4))
            ->method('getOption')
            ->with(
                $this->logicalOr(
                    $this->equalTo(LoginCommand::OPT_HOMEDIR),
                    $this->equalTo(LoginCommand::ARG_APITOKEN),
                    $this->equalTo(LoginCommand::ARG_USERNAME),
                    $this->equalTo(LoginCommand::ARG_PASSWORD)
                )
            )
            ->willReturnCallback(
                function ($option) use ($apitoken, $username, $password) {
                    if (LoginCommand::OPT_HOMEDIR === $option) {
                        return null;
                    } else {
                        if (LoginCommand::ARG_APITOKEN === $option) {
                            return $apitoken;
                        } else {
                            if (LoginCommand::ARG_USERNAME === $option) {
                                return $username;
                            } else {
                                return $password;
                            }
                        }
                    }
                }
            );

        $outputMock = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        if (empty($apitoken) && empty($username) && empty($password)) {
            $outputMock
                ->expects($this->exactly(2))
                ->method('writeln');
            $exptectedReturnCode = 1;
        } else {
            if (!empty($apitoken) && empty($username) && empty($password)) {
                $this->authServiceMock
                    ->expects($this->once())
                    ->method('loginWithToken')
                    ->with($this->equalTo($apitoken))
                    ->willReturn($loginSuccess);
            } else {
                if (empty($apitoken) && !empty($username) && !empty($password)) {
                    $this->authServiceMock
                        ->expects($this->once())
                        ->method('loginWithCredentials')
                        ->with(
                            $this->equalTo($username),
                            $this->equalTo($password)
                        )
                        ->willReturn($loginSuccess);
                }
                else {
                    $exptectedReturnCode = 1;
                }
            }
        }

        if(!$loginSuccess) {
            $exptectedReturnCode = 1;
        }

        $ref = new \ReflectionClass($this->command);
        $methode = $ref->getMethod('execute');
        $methode->setAccessible(true);

        $this->assertEquals($exptectedReturnCode, $methode->invoke($this->command, $inputMock, $outputMock));
    }
}
