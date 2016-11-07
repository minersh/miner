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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoginCommand extends MinerCommand
{
    const ARG_REALMURL = 'realmurl';
    const OPT_USERNAME = 'username';
    const OPT_PASSWORD = 'password';
    const OPT_APITOKEN = 'apitoken';

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
            ->addArgument(
                self::ARG_REALMURL,
                InputArgument::REQUIRED,
                "Redmine URL eg. 'https://example.com/redmine/' or 'http://redmine.example.com'"
            )
            ->addOption(self::OPT_USERNAME, null, InputOption::VALUE_OPTIONAL, "Redmine username")
            ->addOption(self::OPT_PASSWORD, null, InputOption::VALUE_OPTIONAL, "Redmine password")
            ->addOption(self::OPT_APITOKEN, null, InputOption::VALUE_OPTIONAL, "Redmine apitoken");
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

        $realmurl = $input->getArgument(self::ARG_REALMURL);
        $username = $input->getOption(self::OPT_USERNAME);
        $password = $input->getOption(self::OPT_PASSWORD);
        $apiToken = $input->getOption(self::OPT_APITOKEN);

        if (empty($realmurl) || !filter_var($realmurl, FILTER_VALIDATE_URL)) {
            $output->writeln(
                "<error>[!] ERROR: Please provide a valid Redmine URL of your System!</error>"
            );
            return 1;
        }

        if (!empty($apiToken)) {
            $success = $this->authService->loginWithToken($realmurl, $apiToken);
        } else {
            if (empty($username) || empty($password)) {
                $output->writeln(
                    "<error>[!] ERROR: Please provide the API-token or your username and password!</error>"
                );
                return 1;
            }
            $success = $this->authService->loginWithCredentials($realmurl, $username, $password);
        }

        if (!$success) {
            $output->writeln("<error>[!] ERROR: Invalid API-token or credentials. Please try again.</error>");
            return 1;
        } else {
            $user = $this->authService->getUser();
            $output->writeln(
                sprintf(
                    "Welcome <info>%s</info>, you are now logged in!",
                    $user->getLogin()
                )
            );
        }

        return 0;
    }
}
