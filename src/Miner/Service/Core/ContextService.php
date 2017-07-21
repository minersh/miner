<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Core;

use Miner\Exceptions\AuthException;
use Miner\Model\Project\Project;
use Miner\Service\Auth\AuthService;
use Miner\Service\Redmine\RedmineApi;

class ContextService
{
    const FILE_CONTEXT = 'context.json';

    const VAR_PROJECT_ID = 'project_id';

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var RedmineApi
     */
    private $redmineApi;

    /**
     * ContextService constructor.
     *
     * @param EnvironmentService $environmentService
     * @param AuthService $authService
     * @param RedmineApi $redmineApi
     */
    public function __construct(
        EnvironmentService $environmentService,
        AuthService $authService,
        RedmineApi $redmineApi
    ) {
        $this->environmentService = $environmentService;
        $this->authService = $authService;
        $this->redmineApi = $redmineApi;
    }

    /**
     * @return \Miner\Model\User\User|null
     */
    public function getUser()
    {
        try {
            return $this->authService->getUser();
        } catch (AuthException $authException) {
            return null;
        }
    }

    /**
     * @return null|Project
     */
    public function getProject()
    {
        $context = $this->getContextData();
        if (!isset($context[self::VAR_PROJECT_ID]) || empty($context[self::VAR_PROJECT_ID])) {
            return null;
        }

        return $this->redmineApi
            ->getProjectApi()
            ->getProject((int)$context['project_id']) ?: null;
    }

    /**
     * @param Project $project
     *
     * @return bool
     */
    public function setProject(Project $project)
    {
        return $this->storeContextData(
            [
                self::VAR_PROJECT_ID => $project->getId(),
            ]
        );
    }

    /**
     * @return bool
     */
    public function unsetProject()
    {
        return $this->storeContextData(
            [
                self::VAR_PROJECT_ID => null,
            ]
        );
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function storeContextData(array $data)
    {
        $context = $this->getContextData();
        $context = array_merge($context, $data);

        $filename = $this->environmentService->getHomedir() . '/' . self::FILE_CONTEXT;
        if (file_put_contents($filename, $this->environmentService->encodeJson($context)) > 0) {
            chmod($filename, 0600);
            return true;
        }

        // @codeCoverageIgnoreStart
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return array
     */
    private function getContextData()
    {
        $filename = $this->environmentService->getHomedir() . '/' . self::FILE_CONTEXT;
        if (file_exists($filename)) {
            $raw = file_get_contents($filename);
            if ($raw) {
                return json_decode($raw, true) ?: null;
            }
        }
        return [];
    }
}
