<?php
/**
 * @copyright Copyright (c) 1999-2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Test\Unit\Service\Auth;

use Miner\Exceptions\AuthException;
use Miner\Factory\UserFactory;
use Miner\Model\User\User;
use Miner\Registry\User\UserRegistry;
use Miner\Service\Auth\AuthService;
use Miner\Service\Core\EnvironmentService;
use Miner\Service\Redmine\Auth\RedmineAuthApi;
use Miner\Service\Redmine\RedmineApi;
use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class AuthServiceTest
 *
 * @covers \Miner\Service\Auth\AuthService
 */
class AuthServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthService
     */
    private $service;

    /**
     * @var RedmineApi|Mock
     */
    private $redmineApi;

    /**
     * @var EnvironmentService|Mock
     */
    private $environmentService;

    /**
     * @var UserRegistry
     */
    private $userRegistry;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->redmineApi = $this->getMockBuilder(RedmineApi::class)
            ->disableOriginalConstructor()
            ->setMethods(['createClientContextByToken', 'getAuthApi'])
            ->getMock();

        $this->environmentService = $this->getMockBuilder(EnvironmentService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserData', 'storeUserData'])
            ->getMock();

        $this->userRegistry = new UserRegistry(new UserFactory());

        $this->service = new AuthService($this->redmineApi, $this->environmentService, $this->userRegistry);
    }

    /**
     * @return array
     */
    public function getUserDataProvider()
    {
        return [
            [
                // $userData
                [
                    'realmurl' => 'http://example.com/',
                    'userdata' => ['id' => 123, 'api_key' => '12345'],
                    'user' => ['id' => 123],
                ],

                // $loginSuccess
                true,
            ],
            [
                // $userData
                null,

                // $loginSuccess
                false,
            ],
            [
                // $userData
                [
                    'realmurl' => 'http://example.com/',
                    'userdata' => ['id' => 123, 'api_key' => '12345'],
                    'user' => ['id' => 123],
                ],

                // $loginSuccess
                false,
            ],
        ];
    }

    /**
     * @dataProvider getUserDataProvider
     *
     * @param array|null $userData
     * @param bool $loginSuccess
     */
    public function testGetUser(array $userData = null, $loginSuccess)
    {
        $this->environmentService
            ->expects($this->once())
            ->method('getUserData')
            ->willReturn($userData);

        $this->assertAttributeEquals(null, 'currentUser', $this->service);

        if ($userData) {

            $authApiMock = $this->getMockBuilder(RedmineAuthApi::class)
                ->disableOriginalConstructor()
                ->setMethods(['login'])
                ->getMock();

            $this->redmineApi
                ->expects($this->once())
                ->method('createClientContextByToken')
                ->with(
                    $this->equalTo($userData['realmurl']),
                    $this->equalTo($userData['userdata']['api_key'])
                )
                ->willReturn($this->redmineApi);

            $this->redmineApi
                ->expects($this->once())
                ->method('getAuthApi')
                ->willReturn($authApiMock);

            $authApiMock
                ->expects($this->once())
                ->method('login')
                ->willReturn($loginSuccess ? $userData : false);

            if ($loginSuccess) {

                $this->environmentService
                    ->expects($this->once())
                    ->method('storeUserData')
                    ->with(
                        $this->equalTo($userData['realmurl']),
                        $this->equalTo($userData['user'])
                    );

                $user = $this->service->getUser();

                $this->assertAttributeEquals(
                    $userData['userdata'],
                    'currentUser',
                    $this->service
                );

                $this->assertInstanceOf(User::class, $user);
                $this->assertEquals($userData['userdata'], $user->getModelData());

            } else {
                $this->expectException(AuthException::class);
                $this->expectExceptionMessage(AuthException::badApiToken()->getMessage());

                $this->service->getUser();
            }
        } else {
            $this->expectException(AuthException::class);
            $this->expectExceptionMessage(AuthException::noUserConfigured()->getMessage());

            $this->service->getUser();
        }
    }


//    public function testLoginWithCredentials()
//    {
//        // TODO implement test
//    }
}
