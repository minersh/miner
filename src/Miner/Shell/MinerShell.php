<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Shell;

use Pimple\Container;
use Psy\Command\ClearCommand;
use Psy\Command\ExitCommand;
use Psy\Command\HelpCommand;
use Psy\Configuration;
use Psy\Shell;

class MinerShell extends Shell
{
    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var Container
     */
    private $container;

    /**
     * MinerShell constructor.
     *
     * @param Container $container
     * @param Configuration|null $config
     */
    public function __construct(Container $container, Configuration $config = null)
    {
        parent::__construct($config);
        $this->addCommands($this->getDefaultCommands());
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return (string)$this->container['version'];
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return sprintf(
            "\r\n<aside>Miner interactive shell v%s by Simon Schröer <http://miner.sh></aside>\r\n",
            $this->getVersion()
        );
    }

    /**
     * @return array
     */
    protected function getDefaultCommands()
    {
        return [
            // Default Psy Sh Commands
            new HelpCommand(),
            new ClearCommand(),
            new ExitCommand(),
        ];
    }
}
