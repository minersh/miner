<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Service\Redmine\Auth;

use Miner\Service\Redmine\Auth\RedmineAuthApi;
use Miner\Service\Redmine\RedmineApi;
use Miner\Service\Redmine\RedmineSubApi;
use Miner\Service\Redmine\User\RedmineUserApi;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Redmine\Client;

/**
 * Class RedmineAuthApiTest
 *
 * @covers \Miner\Service\Redmine\Auth\RedmineAuthApi
 */
class RedmineAuthApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedmineAuthApi
     */
    private $service;

    /**
     * @var Mock|RedmineApi
     */
    private $remineApiMock;

    /**
     * @var Mock|Client
     */
    private $clientMock;

    public function setUp()
    {
        $this->remineApiMock = $this->getMockBuilder(RedmineApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new RedmineAuthApi();
        $this->service->setClient($this->remineApiMock, $this->clientMock);
    }

    public function testClass()
    {
        $this->assertInstanceOf(RedmineSubApi::class, $this->service);
    }

    public function testLogin()
    {
        $userdata = [
            'user' => [
                'id' => 123
            ]
        ];

        $userMock = $this->getMockBuilder(RedmineUserApi::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCurrentUserData'])
            ->getMock();

        $userMock
            ->expects($this->once())
            ->method('getCurrentUserData')
            ->willReturn($userdata);

        $this->remineApiMock
            ->expects($this->once())
            ->method('getUserApi')
            ->willReturn($userMock);

        $this->assertEquals($userdata, $this->service->login());
    }
}
