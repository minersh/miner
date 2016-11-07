<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Service\Redmine;

use Miner\Service\Redmine\RedmineApi;
use Miner\Service\Redmine\RedmineSubApi;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use Redmine\Client;

/**
 * Class RedmineSubApiTest
 *
 * @covers \Miner\Service\Redmine\RedmineSubApi
 */
class RedmineSubApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedmineSubApi|Mock
     */
    private $service;

    public function setUp()
    {
        $this->service = $this->getMockForAbstractClass(RedmineSubApi::class);
    }

    public function test()
    {
        $ref = new \ReflectionClass($this->service);

        $getRedmineApi = $ref->getMethod('getRedmineApi');
        $getRedmineApi->setAccessible(true);

        $getClient = $ref->getMethod('getClient');
        $getClient->setAccessible(true);

        $this->assertNull($getRedmineApi->invoke($this->service));
        $this->assertNull($getClient->invoke($this->service));

        $redmineApiMock = $this->getMockBuilder(RedmineApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertSame($this->service, $this->service->setClient($redmineApiMock, $clientMock));
        $this->assertAttributeSame($redmineApiMock, 'redmineApi', $this->service);
        $this->assertAttributeSame($clientMock, 'client', $this->service);

        $this->assertSame($redmineApiMock, $getRedmineApi->invoke($this->service));
        $this->assertSame($clientMock, $getClient->invoke($this->service));
    }
}
