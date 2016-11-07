<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/functions.php";

use Symfony\Component\Console\Application;
use Symfony\Component\Yaml\Yaml;
use Pimple\Container;

/*
 * Configure DI
 */
$diContainer = new Container();

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

    if ($instance instanceof \Miner\Command\MinerCommand) {
        $instance->setEnvironmentService($diContainer['miner.core.environment']);
    }

    $commandList[] = $instance;
}

/*
 * Configure Application and Commands
 */
$app = new Application();
$app->addCommands($commandList);

return $app;
