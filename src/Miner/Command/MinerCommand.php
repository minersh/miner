<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class MinerCommand extends Command
{
    const OPT_HOMEDIR = 'home';

    /**
     * @var string
     */
    private $homeDir;

    /**
     * MinerCommand constructor.
     */
    public function __construct()
    {
        parent::__construct(null);

        /*
         * Add default options
         */
        $this->addOption(
            self::OPT_HOMEDIR,
            null,
            InputOption::VALUE_OPTIONAL,
            "Defines the home directory to use for this miner installation"
        );
    }

    /**
     * @return string
     */
    protected function getHomeDir(): string
    {
        return $this->homeDir;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->homeDir = $input->getOption(self::OPT_HOMEDIR);
        if (empty($this->homeDir)) {
            $this->homeDir = trim(`cd && pwd`) . '/.miner';
        }

        $output->writeln(
            sprintf(
                "[!] Using Home dir: <info>%s</info>",
                $this->homeDir
            ),
            OutputInterface::VERBOSITY_VERBOSE
        );

        return 0;
    }
}
