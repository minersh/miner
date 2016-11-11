<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command\Core;

use Miner\Command\MinerCommand;
use Miner\Shell\MinerShell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psy\Output\ShellOutput;

class InteractiveCommand extends MinerCommand
{
    /**
     * @var MinerShell
     */
    private $minerShell;

    /**
     * @var ShellOutput
     */
    private $shellOutput;

    /**
     * InteractiveCommand constructor.
     *
     * @param MinerShell $minerShell
     * @param ShellOutput $shellOutput
     */
    public function __construct(MinerShell $minerShell, ShellOutput $shellOutput)
    {
        parent::__construct(null);
        $this->minerShell = $minerShell;
        $this->shellOutput = $shellOutput;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('interactive')
            ->setAliases(['ia', 'shell'])
            ->setDescription("Opens the miner interactive shell.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $help = "At the prompt, type <comment>help</comment> for some help.\r\n" .
            "To exit the shell, type <comment>^D</comment>.";

        $this->shellOutput->writeln($help);
        $this->minerShell->run($input, $this->shellOutput);

        return 0;
    }
}
