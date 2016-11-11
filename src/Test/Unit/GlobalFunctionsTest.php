<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Test\Unit;

use Pimple\Container;

if (!function_exists('resolveDiArguments')) {
    require __DIR__ . '/../../functions.php';
}

class GlobalFunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function testResolveDiArgumentsDataProvider()
    {
        return [
            [
                new Container(),
                ['test123'],
                ['test123']
            ],
            [
                new Container(['service123' => 'instance123']),
                ['test456', '@service123', ['ignored']],
                ['test456', 'instance123', ['ignored']]
            ],
        ];
    }

    /**
     * @param Container $container
     * @param array $rawArguments
     * @param array $exptectedArguments
     *
     * @dataProvider testResolveDiArgumentsDataProvider
     */
    public function testResolveDiArguments(Container $container, array $rawArguments, array $exptectedArguments)
    {
        $this->assertEquals(
            $exptectedArguments,
            resolveDiArguments($rawArguments, $container)
        );
    }
}
