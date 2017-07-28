<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Shell;

use Miner\Shell\MinerShell;
use Pimple\Container;
use Psy\Command\ClearCommand;
use Psy\Command\ExitCommand;
use Psy\Command\HelpCommand;

/**
 * Class Test
 *
 * @covers \Miner\Shell\MinerShell
 */
class Test extends \PHPUnit_Framework_TestCase
{
    const VERSION = '1.2.3';

    /**
     * @var Container
     */
    private $containerMock;

    /**
     * @var MinerShell
     */
    private $shell;

    public function setUp()
    {
        $this->containerMock = new Container(['version' => self::VERSION]);
        $this->shell = new MinerShell($this->containerMock);
    }

    public function testVersion()
    {
        $this->assertEquals(self::VERSION, $this->shell->getVersion());
    }

    public function testGetHeader()
    {
        $ref = new \ReflectionClass($this->shell);
        $method = $ref->getMethod('getHeader');
        $method->setAccessible(true);

        $this->assertEquals(
            sprintf(
                "<aside>Miner interactive shell v%s by Simon Schröer</aside>\r\n",
                self::VERSION
            ),
            $method->invoke($this->shell)
        );
    }

    public function testGetDefaultCommands()
    {
        $ref = new \ReflectionClass($this->shell);
        $method = $ref->getMethod('getDefaultCommands');
        $method->setAccessible(true);

        $defaultCommands = [
            HelpCommand::class,
            ClearCommand::class,
            ExitCommand::class,
        ];
        $testCommands = $method->invoke($this->shell);
        foreach ($testCommands as $i => $command) {
            $this->assertInstanceOf($defaultCommands[$i], $command);
        }
    }
}
