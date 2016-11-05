<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

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
