<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Command\Auth;

use Miner\Command\MinerCommand;
use Miner\Service\Auth\AuthService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoginCommand extends MinerCommand
{
    const ARG_USERNAME = 'username';
    const ARG_PASSWORD = 'password';
    const ARG_APITOKEN = 'apitoken';

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
        $this->setName('auth:login');
        $this
            ->addOption(self::ARG_USERNAME, null, InputOption::VALUE_OPTIONAL, "Redmine username")
            ->addOption(self::ARG_PASSWORD, null, InputOption::VALUE_OPTIONAL, "Redmine password")
            ->addOption(self::ARG_APITOKEN, null, InputOption::VALUE_OPTIONAL, "Redmine apitoken");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $username = $input->getOption(self::ARG_USERNAME);
        $password = $input->getOption(self::ARG_PASSWORD);
        $apiToken = $input->getOption(self::ARG_APITOKEN);

        if (!empty($apiToken)) {
            $success = $this->authService->loginWithToken($apiToken);
        } else {
            if (empty($username) || empty($password)) {
                $output->writeln(
                    "<error>[!] ERROR: Please provide the API-token or your username and password!</error>"
                );
                return 1;
            }
            $success = $this->authService->loginWithCredentials($username, $password);
        }

        if (!$success) {
            $output->writeln("<error>[!] ERROR: Invalid API-token or credentials. Please try again.</error>");
            return 1;
        }

        return 0;
    }
}
