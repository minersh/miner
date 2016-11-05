<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

require __DIR__ . "/../vendor/autoload.php";

use Symfony\Component\Console\Application;
use Symfony\Component\Yaml\Yaml;
use Pimple\Container;

/**
 * @param array $rawArguments
 * @param Container $diContainer
 *
 * @return array
 */
function resolveDiArguments(array $rawArguments, Container $diContainer)
{
    $arguments = [];
    if (!empty($rawArguments)) {
        foreach ($rawArguments as $argument) {
            if (
                !is_array($argument)
                && !empty($argument)
                && '@' === (string)$argument[0]
            ) {
                $argument = $diContainer[substr($argument, 1)];
            }
            $arguments[] = $argument;
        }
    }
    return $arguments;
}

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
