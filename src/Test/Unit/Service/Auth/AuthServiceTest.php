<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Service\Auth;

use Miner\Exceptions\AuthException;
use Miner\Registry\User\UserRegistry;
use Miner\Service\Auth\AuthService;
use Miner\Service\Core\EnvironmentService;
use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class AuthServiceTest
 *
 * @covers \Miner\Service\Auth\AuthService
 */
class AuthServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthService
     */
    private $service;

    /**
     * @var Mock|EnvironmentService
     */
    private $environmentMock;

    /**
     * @var Mock|UserRegistry
     */
    private $userRegistryMock;

    public function setUp()
    {
        $this->environmentMock = $this->getMockBuilder(EnvironmentService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userRegistryMock = $this->getMockBuilder(UserRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new AuthService($this->environmentMock, $this->userRegistryMock);
    }

    public function testGetUser()
    {
        $userdata = ['id' => 123];
        $fakeUserReturn = 'User Return Value';

        $this->environmentMock
            ->expects($this->once())
            ->method('getUserData')
            ->willReturn($userdata);

        $this->userRegistryMock
            ->expects($this->once())
            ->method('getInstanceByData')
            ->with($this->equalTo($userdata))
            ->willReturn($fakeUserReturn);

        $this->assertEquals($fakeUserReturn, $this->service->getUser());
    }

    public function testGetUserException()
    {
        $userdata = [];

        $this->environmentMock
            ->expects($this->once())
            ->method('getUserData')
            ->willReturn($userdata);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage(AuthException::noUserConfigured()->getMessage());

        $this->service->getUser();
    }

    public function testLoginWithToken()
    {
        $this->assertFalse($this->service->loginWithToken('token'));
    }

    public function testLoginWithCredentials()
    {
        $this->assertFalse($this->service->loginWithCredentials('user', 'pass'));
    }
}
