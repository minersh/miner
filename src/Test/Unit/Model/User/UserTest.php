<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit\Model\User;

use Miner\Model\User\User;

/**
 * Class UserTest
 *
 * @covers \Miner\Model\User\User
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    public function dataProvider()
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

        return [
            [[], []],
            [$userdata, []],
            [[], $userdata],
            [$userdata, $userdata],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array $constructorData
     * @param array $userdata
     */
    public function test(array $constructorData, array $userdata)
    {
        $user = new User($constructorData);
        $this->assertAttributeEquals($constructorData, 'userdata', $user);

        $user->setUserdata($userdata);
        $this->assertAttributeEquals($userdata, 'userdata', $user);

        if (!empty($userdata)) {
            $this->assertEquals($userdata['id'], $user->getId());
            $this->assertEquals($userdata['login'], $user->getLogin());
            $this->assertEquals($userdata['firstname'], $user->getFirstname());
            $this->assertEquals($userdata['lastname'], $user->getLastname());
            $this->assertEquals($userdata['mail'], $user->getMail());
            $this->assertEquals($userdata['api_key'], $user->getApiKey());
            $this->assertEquals($userdata['status'], $user->getStatus());
        }
    }
}
