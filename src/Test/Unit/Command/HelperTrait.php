<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Command;

use Miner\Command\MinerCommand;
use Miner\Service\Core\EnvironmentService;
use PHPUnit_Framework_MockObject_MockObject as Mock;

trait HelperTrait
{
    /**
     * @var EnvironmentService|Mock
     */
    protected $environmentServiceMock;

    /**
     * @param MinerCommand $command
     * @param array $methods
     *
     * @return MinerCommand
     */
    protected function prepareCommand(MinerCommand $command, array $methods = [])
    {
        $addHomeDirDefault = !in_array('getHomedir', $methods);
        if ($addHomeDirDefault) {
            $methods[] = 'getHomedir';
        }

        $this->environmentServiceMock = $this->getMockBuilder(EnvironmentService::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();

        if ($addHomeDirDefault) {
            $this->environmentServiceMock
                ->expects($this->any())
                ->method('getHomedir')
                ->willReturn('/tmp/miner-test-home-dir');
        }

        $command->setEnvironmentService($this->environmentServiceMock);

        return $command;
    }
}
