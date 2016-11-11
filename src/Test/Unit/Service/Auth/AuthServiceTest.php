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
use Miner\Service\Redmine\Auth\RedmineAuthApi;
use Miner\Service\Redmine\RedmineApi;
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
     * @var Mock|RedmineApi
     */
    private $redmineApiMock;

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
        $this->redmineApiMock = $this->getMockBuilder(RedmineApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->environmentMock = $this->getMockBuilder(EnvironmentService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userRegistryMock = $this->getMockBuilder(UserRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new AuthService(
            $this->redmineApiMock,
            $this->environmentMock,
            $this->userRegistryMock
        );
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

    public function loginWithTokenProvider()
    {
        return [
            ['url', 'token', ['user' => ['id' => 123]]],
            ['url', 'token', ['invalid array']],
            ['url', 'token', []],
            ['url', 'token', null]
        ];
    }

    /**
     * @param $realmurl
     * @param $apitoken
     * @param $userdata
     *
     * @dataProvider loginWithTokenProvider
     */
    public function testLoginWithToken($realmurl, $apitoken, $userdata)
    {
        $authApiMock = $this->getMockBuilder(RedmineAuthApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $authApiMock
            ->expects($this->once())
            ->method('login')
            ->willReturn($userdata);

        $this->redmineApiMock
            ->expects($this->once())
            ->method('createClientContextByToken')
            ->with(
                $this->equalTo($realmurl),
                $this->equalTo($apitoken)
            )
            ->willReturn($this->redmineApiMock);

        $this->redmineApiMock
            ->expects($this->once())
            ->method('getAuthApi')
            ->willReturn($authApiMock);

        if (isset($userdata['user'])) {
            $this->environmentMock
                ->expects($this->once())
                ->method('storeUserData')
                ->with($this->equalTo($userdata['user']));
            $this->assertTrue($this->service->loginWithToken($realmurl, $apitoken));
        } else {
            $this->assertFalse($this->service->loginWithToken($realmurl, $apitoken));
        }
    }

    public function loginWithCredentialsProvider()
    {
        return [
            ['url', 'user', 'pass', ['user' => ['id' => 123]]],
            ['url', 'user', 'pass', ['invalid array']],
            ['url', 'user', 'pass', []],
            ['url', 'user', 'pass', null]
        ];
    }

    /**
     * @param $realmurl
     * @param $username
     * @param $password
     * @param $userdata
     *
     * @dataProvider loginWithCredentialsProvider
     */
    public function testLoginWithCredentials($realmurl, $username, $password, $userdata)
    {
        $authApiMock = $this->getMockBuilder(RedmineAuthApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $authApiMock
            ->expects($this->once())
            ->method('login')
            ->willReturn($userdata);

        $this->redmineApiMock
            ->expects($this->once())
            ->method('createClientContextByCredentials')
            ->with(
                $this->equalTo($realmurl),
                $this->equalTo($username),
                $this->equalTo($password)
            )
            ->willReturn($this->redmineApiMock);

        $this->redmineApiMock
            ->expects($this->once())
            ->method('getAuthApi')
            ->willReturn($authApiMock);

        if (isset($userdata['user'])) {
            $this->environmentMock
                ->expects($this->once())
                ->method('storeUserData')
                ->with($this->equalTo($userdata['user']));
            $this->assertTrue($this->service->loginWithCredentials($realmurl, $username, $password));
        } else {
            $this->assertFalse($this->service->loginWithCredentials($realmurl, $username, $password));
        }
    }
}
