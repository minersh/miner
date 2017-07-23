<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Factory;

use Miner\Factory\ProjectFactory;
use Miner\Model\Project\Project;

/**
 * Class ProjectFactoryTest
 *
 * @covers \Miner\Factory\ProjectFactory
 */
class ProjectFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = new ProjectFactory();

        $result = $factory->create();
        $this->assertInstanceOf(Project::class, $result);
        $this->assertAttributeSame($factory, 'projectFactory', $result);
        $this->assertAttributeEquals([], 'projectData', $result);
    }

    public function testCreateByProjectdata()
    {
        $factory = new ProjectFactory();

        $projectData = [
            'id' => 123,
        ];

        $result = $factory->createByProjectdata($projectData);
        $this->assertInstanceOf(Project::class, $result);
        $this->assertAttributeSame($factory, 'projectFactory', $result);
        $this->assertAttributeEquals($projectData, 'projectData', $result);
    }
}
