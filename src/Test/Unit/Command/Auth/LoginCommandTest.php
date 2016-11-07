<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Command\Auth;

use Miner\Command\Auth\LoginCommand;
use Miner\Model\User\User;
use Miner\Service\Auth\AuthService;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Test\Unit\Command\HelperTrait;

/**
 * Class LoginCommandTest
 *
 * @covers \Miner\Command\Auth\LoginCommand
 */
class LoginCommandTest extends \PHPUnit_Framework_TestCase
{
    use HelperTrait;

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

        $this->command = $this->prepareCommand(new LoginCommand($this->authServiceMock));
    }

    public function executeDataProvider()
    {
        return [
            ['', '', '', '', false],
            ['example', 'token', '', '', false],
            ['http://example.com', 'token', '', '', true],
            ['http://example.com', '', 'user', 'pass', true],
            ['http://example.com', '', 'user', '', false],
            ['http://example.com', 'token', 'user', '', false],
            ['http://example.com', 'token', 'user', 'pass', false]
        ];
    }

    /**
     * @param string $realmurl
     * @param string $apitoken
     * @param string $username
     * @param string $password
     * @param bool $loginSuccess
     *
     * @dataProvider executeDataProvider
     */
    public function testExecute($realmurl, $apitoken, $username, $password, $loginSuccess)
    {
        $exptectedReturnCode = 0;

        $inputMock = $this->getMockBuilder(InputInterface::class)
            ->getMock();

        $inputMock
            ->expects($this->once())
            ->method('getArgument')
            ->with($this->equalTo(LoginCommand::ARG_REALMURL))
            ->willReturn($realmurl);

        $inputMock
            ->expects($this->exactly(4))
            ->method('getOption')
            ->with(
                $this->logicalOr(
                    $this->equalTo(LoginCommand::OPT_HOMEDIR),
                    $this->equalTo(LoginCommand::OPT_APITOKEN),
                    $this->equalTo(LoginCommand::OPT_USERNAME),
                    $this->equalTo(LoginCommand::OPT_PASSWORD)
                )
            )
            ->willReturnCallback(
                function ($option) use ($apitoken, $username, $password) {
                    if (LoginCommand::OPT_HOMEDIR === $option) {
                        return null;
                    } else {
                        if (LoginCommand::OPT_APITOKEN === $option) {
                            return $apitoken;
                        } else {
                            if (LoginCommand::OPT_USERNAME === $option) {
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

        if (filter_var($realmurl, FILTER_VALIDATE_URL)) {
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
                        ->with(
                            $this->equalTo($realmurl),
                            $this->equalTo($apitoken)
                        )
                        ->willReturn($loginSuccess);
                } else {
                    if (empty($apitoken) && !empty($username) && !empty($password)) {
                        $this->authServiceMock
                            ->expects($this->once())
                            ->method('loginWithCredentials')
                            ->with(
                                $this->equalTo($realmurl),
                                $this->equalTo($username),
                                $this->equalTo($password)
                            )
                            ->willReturn($loginSuccess);
                    } else {
                        $exptectedReturnCode = 1;
                    }
                }
            }
        }

        if ($loginSuccess) {
            $userMock = $this->getMockBuilder(User::class)
                ->disableOriginalConstructor()
                ->setMethods(['getLogin'])
                ->getMock();

            $userMock
                ->expects($this->once())
                ->method('getLogin')
                ->willReturn($username);

            $this->authServiceMock
                ->expects($this->once())
                ->method('getUser')
                ->willReturn($userMock);
        } else {
            $exptectedReturnCode = 1;
        }

        $ref = new \ReflectionClass($this->command);
        $methode = $ref->getMethod('execute');
        $methode->setAccessible(true);

        $this->assertEquals($exptectedReturnCode, $methode->invoke($this->command, $inputMock, $outputMock));
    }
}
