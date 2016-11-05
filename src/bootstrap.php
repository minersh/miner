<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

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
        $arguments = [];
        if (isset($serviceConfig['arguments']) && !empty($serviceConfig['arguments'])) {
            foreach ($serviceConfig['arguments'] as $argument) {
                if (!is_array($argument) && !empty($argument) && '@' === (string)$argument[0]) {
                    $argument = $diContainer[substr($argument, 1)];
                }
                $arguments[] = $argument;
            }
        }

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
    $arguments = [];
    if (!empty($commandArgs) && is_array($commandArgs)) {
        foreach ($commandArgs as $argument) {
            if (!is_array($argument) && !empty($argument) && '@' === (string)$argument[0]) {
                $argument = $diContainer[substr($argument, 1)];
            }
            $arguments[] = $argument;
        }
    }

    $reflector = new ReflectionClass($commandClass);
    if ($reflector->getConstructor() && !empty($arguments)) {
        $commandList[] = $reflector->newInstanceArgs($arguments);
    } else {
        $commandList[] = $reflector->newInstance();
    }
}

/*
 * Configure Application and Commands
 */
$app = new Application();
$app->addCommands($commandList);

return $app;
