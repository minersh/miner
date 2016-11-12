<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Core;

use Miner\Model\Project\Project;
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
     * @var RedmineApi
     */
    private $redmineApi;

    /**
     * ContextService constructor.
     *
     * @param EnvironmentService $environmentService
     * @param RedmineApi $redmineApi
     */
    public function __construct(EnvironmentService $environmentService, RedmineApi $redmineApi)
    {
        $this->environmentService = $environmentService;
        $this->redmineApi = $redmineApi;
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
