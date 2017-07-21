<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/functions.php";

use Miner\Api\EventListenerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Yaml\Yaml;
use Pimple\Container;

/*
 * Configure DI
 */
$diContainer = new Container(['version' => MINER_VERSION]);
$diContainer['container'] = $diContainer;

/*
 * Initialize Application
 */
$diContainer['app'] = new Application('miner', MINER_VERSION);

/*
 * Prepare services
 */
$serviceData = Yaml::parse(file_get_contents(__DIR__ . '/config/services.yml'));
foreach ($serviceData['services'] as $serviceId => $serviceConfig) {
    $diContainer[$serviceId] = function (Container $diContainer) use ($serviceConfig) {
        $arguments = isset($serviceConfig['arguments'])
            ? resolveDiArguments($serviceConfig['arguments'], $diContainer)
            : [];

        $reflector = new ReflectionClass($serviceConfig['class']);
        if ($reflector->getConstructor() && !empty($arguments)) {
            return $reflector->newInstanceArgs($arguments);
        }
        return $reflector->newInstance();
    };
}

/*
 * Prepare Events
 */
$eventDispatcher = $diContainer['miner.core.event.dispatcher'];
$eventData = Yaml::parse(file_get_contents(__DIR__ . '/config/events.yml'));
foreach ($eventData['events']['listeners'] as $eventId => $serviceIds) {
    foreach ($serviceIds as $serviceId) {
        if (isset($diContainer[$serviceId])) {
            $listener = $diContainer[$serviceId];
            if ($listener instanceof EventListenerInterface) {
                $eventDispatcher->addListener($eventId, [$listener, 'handleEvent']);
            }
        }
    }
}

/*
 * Prepare commands
 */
$commandList = [];
$commandData = Yaml::parse(file_get_contents(__DIR__ . '/config/commands.yml'));
foreach ($commandData['commands'] as $commandClass => $commandArgs) {
    $arguments = !empty($commandArgs)
        ? resolveDiArguments($commandArgs, $diContainer)
        : [];

    $reflector = new ReflectionClass($commandClass);
    if ($reflector->getConstructor() && !empty($arguments)) {
        $instance = $reflector->newInstanceArgs($arguments);
    } else {
        $instance = $reflector->newInstance();
    }
    $commandList[] = $instance;
}

/*
 * Register additional services and commands
 */
$diContainer['app']->setDispatcher($eventDispatcher);
$diContainer['app']->addCommands($commandList);

return $diContainer['app'];
