<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Service\Core;

use Miner\Exceptions\EnvironmentException;
use Miner\Service\Core\EnvironmentService;
use Miner\Service\Core\SetupService;
use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class EnvironmentServiceTest
 *
 * @covers \Miner\Service\Core\EnvironmentService
 */
class EnvironmentServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnvironmentService
     */
    private $service;

    /**
     * @var Mock|SetupService
     */
    private $setupServiceMock;

    public function setUp()
    {
        $this->setupServiceMock = $this->getMockBuilder(SetupService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new EnvironmentService($this->setupServiceMock);
    }

    public function testStoreGetUserData()
    {
        $testdir = '/tmp';
        $testdata = ['id' => 123, 'user' => 'name'];

        $this->service->setHomedirPreference($testdir);
        $this->service->storeUserData($testdata);
        $this->assertEquals($testdata, $this->service->getUserData());
    }

    public function testGetUserDataEmpty()
    {
        $this->service->setHomedirPreference('/tmp/test-home-dir');
        $this->assertNull($this->service->getUserData());
    }

    public function testGetHomedir()
    {
        $dir = '/tmp/home-preference';
        $this->service->setHomedirPreference($dir);
        $this->assertEquals($dir, $this->service->getHomedir());
    }

    public function testGetHomedirError()
    {
        $this->expectException(EnvironmentException::class);
        $this->expectExceptionMessage(EnvironmentException::missingHomedirConfiguration()->getMessage());
        $this->service->getHomedir();
    }
}
