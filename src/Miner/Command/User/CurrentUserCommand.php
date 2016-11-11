<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command\User;

use Miner\Command\MinerCommand;
use Miner\Service\Auth\AuthService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrentUserCommand extends MinerCommand
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * LoginCommand constructor.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('user:current');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->authService->getUser();
        $output->writeln(
            sprintf(
                "Logged in as User <info>%s %s</info> (Login: <info>%s</info>, ID <info>%s</info>)",
                $user->getFirstname(),
                $user->getLastname(),
                $user->getLogin(),
                $user->getId()
            )
        );

        return 0;
    }
}
