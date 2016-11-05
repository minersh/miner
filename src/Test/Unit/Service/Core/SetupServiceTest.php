<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Service\Core;

use Miner\Exceptions\SetupException;
use Miner\Service\Core\SetupService;

/**
 * Class SetupServiceTest
 *
 * @covers \Miner\Service\Core\SetupService
 */
class SetupServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SetupService
     */
    private $service;

    public function setUp()
    {
        $this->service = new SetupService();
    }

    public function testInstallHomeDir()
    {
        $testDir = '/tmp/test-home/dir-' . microtime(true);

        $this->assertFalse(is_dir($testDir));
        $this->assertFalse(file_exists($testDir));

        $this->service->installHomeDir($testDir);

        $this->assertTrue(is_dir($testDir));

        rmdir($testDir);
    }

    public function testInstallHomeDirException()
    {
        $testDir = '/dev/null/dir-' . microtime(true);

        $this->expectException(SetupException::class);
        $this->service->installHomeDir($testDir);
    }
}
