<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Factory;

use Miner\Factory\UserFactory;
use Miner\Model\User\User;

/**
 * Class UserFactoryTest
 *
 * @covers \Miner\Factory\UserFactory
 */
class UserFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new UserFactory();
    }

    public function testCreate()
    {
        $user = $this->factory->create();
        $this->assertInstanceOf(User::class, $user);
        $this->assertAttributeEquals([], 'userdata', $user);
    }

    public function testCreateByUserdata()
    {
        $userdata = [
            'id'        => 123,
            'login'     => 'login',
            'firstname' => 'firstname',
            'lastname'  => 'lastname',
            'mail'      => 'email',
            'api_key'   => 'api-key',
            'status'    => 1,
        ];

        $user = $this->factory->createByUserdata($userdata);
        $this->assertInstanceOf(User::class, $user);
        $this->assertAttributeEquals($userdata, 'userdata', $user);

        $this->assertEquals($userdata['id'], $user->getId());
        $this->assertEquals($userdata['login'], $user->getLogin());
        $this->assertEquals($userdata['firstname'], $user->getFirstname());
        $this->assertEquals($userdata['lastname'], $user->getLastname());
        $this->assertEquals($userdata['mail'], $user->getMail());
        $this->assertEquals($userdata['api_key'], $user->getApiKey());
        $this->assertEquals($userdata['status'], $user->getStatus());
    }
}
