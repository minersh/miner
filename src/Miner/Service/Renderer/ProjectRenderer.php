<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Service\Renderer;

use Miner\Model\Project\Project;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProjectRenderer
 */
class ProjectRenderer extends AbstractRenderer
{
    /**
     * @param \Miner\Model\Project\Project $project
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function render(Project $project, OutputInterface $output)
    {
        $description = $project->getDescription();
        if (strlen($description) > 40) {
            $description = substr($description, 0, 37) . '...';
        }

        $table = new Table($output);
        $table->addRows(
            [
                ['ID', $project->getId()],
                ['Identifier', $project->getIdentifier()],
                ['Name', $project->getName()],
                ['Description', $description],
                ['Parent', $project->getParent() ? $project->getParent()->getName() : '-'],
                ['Status', $project->getStatus()],
                ['Public', $project->isPublic() ? '<info>yes</info>' : '<comment>no</comment>'],
                ['Created On', $project->getCreatedOn()->format('Y-m-d H:i:s')],
                ['Updated On', $project->getUpdatedOn()->format('Y-m-d H:i:s')],
            ]
        );
        $table->render();
    }
}
