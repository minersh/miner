<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Registry\User;

use Miner\Factory\UserFactory;
use Miner\Model\User\User;
use Miner\Registry\User\UserRegistry;
use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class UserRegistryTest
 *
 * @covers \Miner\Registry\User\UserRegistry
 */
class UserRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserRegistry
     */
    private $service;

    /**
     * @var Mock|UserFactory
     */
    private $userFactoryMock;

    public function setUp()
    {
        $this->userFactoryMock = $this->getMockBuilder(UserFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new UserRegistry($this->userFactoryMock);
    }

    public function testGetInstanceByData()
    {
        $userdata = ['id' => 123];

        $userMock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($userdata['id']);

        $this->userFactoryMock
            ->expects($this->once())
            ->method('createByUserdata')
            ->with($this->equalTo($userdata))
            ->willReturn($userMock);

        $this->assertSame($userMock, $this->service->getInstanceByData($userdata));
        $this->assertSame($userMock, $this->service->getInstanceByData($userdata));
    }
}
