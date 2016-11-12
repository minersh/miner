<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class MinerCommand extends Command
{
    /**
     * @param Application $application
     * @param OutputInterface $output
     * @param string $command
     * @param array $inputArgs
     *
     * @return int
     */
    protected function runCommand(
        Application $application,
        OutputInterface $output,
        string $command,
        array $inputArgs = []
    ) {
        $application->setAutoExit(false);

        $returnCode = $application->run(
            new ArrayInput(
                array_merge(
                    $inputArgs,
                    [
                        'command' => $command,
                    ]
                )
            ),
            $output
        );

        $application->setAutoExit(true);

        return $returnCode;
    }
}
