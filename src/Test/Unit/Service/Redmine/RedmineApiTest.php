<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Service\Redmine;

use Miner\Service\Redmine\Auth\RedmineAuthApi;
use Miner\Service\Redmine\RedmineApi;
use Miner\Service\Redmine\User\RedmineUserApi;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Redmine\Client;

/**
 * Class RedmineApiTest
 *
 * @covers \Miner\Service\Redmine\RedmineApi
 */
class RedmineApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedmineApi
     */
    private $service;

    /**
     * @var Mock|RedmineAuthApi
     */
    private $authApiMock;

    /**
     * @var Mock|RedmineUserApi
     */
    private $userApiMock;

    public function setUp()
    {
        $this->authApiMock = $this->getMockBuilder(RedmineAuthApi::class)
            ->disableOriginalConstructor()
            ->setMethods(['setClient'])
            ->getMock();

        $this->userApiMock = $this->getMockBuilder(RedmineUserApi::class)
            ->disableOriginalConstructor()
            ->setMethods(['setClient'])
            ->getMock();

        $this->service = new RedmineApi($this->authApiMock, $this->userApiMock);
    }

    public function testCreateClientContextByToken()
    {
        $url = 'url';
        $token = 'token';

        $this->assertAttributeEquals(null, 'client', $this->service);
        $this->assertAttributeEquals(null, 'url', $this->service);
        $this->assertAttributeEquals(null, 'token', $this->service);
        $this->assertAttributeEquals(null, 'username', $this->service);
        $this->assertAttributeEquals(null, 'password', $this->service);

        $this->assertEquals(
            $this->service,
            $this->service->createClientContextByToken($url, $token)
        );

        $this->assertAttributeEquals(null, 'client', $this->service);
        $this->assertAttributeEquals($url, 'url', $this->service);
        $this->assertAttributeEquals($token, 'token', $this->service);
        $this->assertAttributeEquals(null, 'username', $this->service);
        $this->assertAttributeEquals(null, 'password', $this->service);
    }

    public function testCreateClientContextByCredentials()
    {
        $url = 'url';
        $username = 'user';
        $password = 'pass';

        $this->assertAttributeEquals(null, 'client', $this->service);
        $this->assertAttributeEquals(null, 'url', $this->service);
        $this->assertAttributeEquals(null, 'token', $this->service);
        $this->assertAttributeEquals(null, 'username', $this->service);
        $this->assertAttributeEquals(null, 'password', $this->service);

        $this->assertEquals(
            $this->service,
            $this->service->createClientContextByCredentials($url, $username, $password)
        );

        $this->assertAttributeEquals(null, 'client', $this->service);
        $this->assertAttributeEquals($url, 'url', $this->service);
        $this->assertAttributeEquals(null, 'token', $this->service);
        $this->assertAttributeEquals($username, 'username', $this->service);
        $this->assertAttributeEquals($password, 'password', $this->service);
    }

    public function testGetAuthApi()
    {
        $url = 'http://example.com';
        $token = 'token';

        $this->authApiMock
            ->expects($this->once())
            ->method('setClient')
            ->with(
                $this->equalTo($this->service),
                $this->isInstanceOf(Client::class)
            )
            ->willReturn($this->authApiMock);

        $this->assertAttributeEquals(null, 'client', $this->service);
        $this->service->createClientContextByToken($url, $token);
        $this->assertSame($this->authApiMock, $this->service->getAuthApi());
        $this->assertAttributeInstanceOf(Client::class, 'client', $this->service);
    }

    public function testGetUserApi()
    {
        $url = 'http://example.com';
        $username = 'user';
        $password = 'pass';

        $this->userApiMock
            ->expects($this->once())
            ->method('setClient')
            ->with(
                $this->equalTo($this->service),
                $this->isInstanceOf(Client::class)
            )
            ->willReturn($this->userApiMock);

        $this->assertAttributeEquals(null, 'client', $this->service);
        $this->service->createClientContextByCredentials($url, $username, $password);
        $this->assertSame($this->userApiMock, $this->service->getUserApi());
        $this->assertAttributeInstanceOf(Client::class, 'client', $this->service);
    }
}
