<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Registry;

use Miner\Exceptions\RegistryException;
use Miner\Registry\Registry;

/**
 * Class RegistryTest
 *
 * @covers \Miner\Registry\Registry
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Registry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new Registry();
    }

    public function testHasAndSet()
    {
        $key = 'test-key';
        $value = 'test-value';

        $this->assertFalse($this->registry->has($key));
        $this->assertSame($this->registry, $this->registry->set($key, $value));
        $this->assertTrue($this->registry->has($key));
    }

    public function testGet()
    {
        $key = 'test-key';
        $value = 'test-value';

        $this->registry->set($key, $value);
        $this->assertEquals($value, $this->registry->get($key));

        $errorKey = 'key-without-assigned-value';
        $this->expectException(RegistryException::class);
        $this->expectExceptionMessage(RegistryException::elementNotFound($errorKey)->getMessage());
        $this->registry->get($errorKey);
    }
}
