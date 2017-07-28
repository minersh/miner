<?php
/**
 * @copyright Copyright (c) 1999-2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Test\Unit\Factory;

use Miner\Factory\ProjectFactory;
use Miner\Factory\TicketFactory;
use Miner\Factory\UserFactory;
use Miner\Model\Ticket\Ticket;
use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class TicketFactoryTest
 *
 * @covers \Miner\Factory\TicketFactory
 */
class TicketFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TicketFactory
     */
    private $factory;

    /**
     * @var \Miner\Factory\UserFactory|Mock
     */
    private $userFactory;

    /**
     * @var \Miner\Factory\ProjectFactory|Mock
     */
    private $projectFactory;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->userFactory = new UserFactory();
        $this->projectFactory = new ProjectFactory();
        $this->factory = new TicketFactory($this->userFactory, $this->projectFactory);
    }

    public function testCreate()
    {
        $result = $this->factory->create();

        $this->assertInstanceOf(Ticket::class, $result);
        $this->assertAttributeSame($this->factory, 'ticketFactory', $result);
        $this->assertAttributeSame($this->userFactory, 'userFactory', $result);
        $this->assertAttributeSame($this->projectFactory, 'projectFactory', $result);
        $this->assertAttributeEquals([], 'ticketData', $result);
    }

    public function testCreateByTicketdata()
    {
        $ticketData = [
            'id' => 345
        ];

        $result = $this->factory->createByTicketdata($ticketData);

        $this->assertInstanceOf(Ticket::class, $result);
        $this->assertAttributeSame($this->factory, 'ticketFactory', $result);
        $this->assertAttributeSame($this->userFactory, 'userFactory', $result);
        $this->assertAttributeSame($this->projectFactory, 'projectFactory', $result);
        $this->assertAttributeEquals($ticketData, 'ticketData', $result);
    }
}
