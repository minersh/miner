<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Service\Redmine\User;

use Miner\Factory\UserFactory;
use Miner\Service\Redmine\RedmineApi;
use Miner\Service\Redmine\RedmineSubApi;
use Miner\Service\Redmine\User\RedmineUserApi;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Redmine\Api\User;
use Redmine\Client;

/**
 * Class RedmineUserApiTest
 *
 * @covers \Miner\Service\Redmine\User\RedmineUserApi
 */
class RedmineUserApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedmineUserApi
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

    /**
     * @var UserFactory
     */
    private $userFactory;

    public function setUp()
    {
        $this->remineApiMock = $this->getMockBuilder(RedmineApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['__get'])
            ->getMock();

        $this->userFactory = new UserFactory();

        $this->service = new RedmineUserApi($this->userFactory);
        $this->service->setClient($this->remineApiMock, $this->clientMock);
    }

    public function testClass()
    {
        $this->assertInstanceOf(RedmineSubApi::class, $this->service);
    }

    public function testGetCurrentUserData()
    {
        $userdata = [
            'user' => [
                'id' => 123
            ]
        ];

        $userMock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userMock
            ->expects($this->once())
            ->method('getCurrentUser')
            ->willReturn($userdata);

        $this->clientMock
            ->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('user'))
            ->willReturn($userMock);

        $this->assertEquals($userdata, $this->service->getCurrentUserData());
    }
}
