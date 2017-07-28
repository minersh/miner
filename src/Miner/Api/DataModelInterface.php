<?php
/**
 * @copyright Copyright (c) 1999-2017 netz98 GmbH (http://www.netz98.de)
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Miner\Api;

/**
 * Interface DataModelInterface
 *
 * @api
 */
interface DataModelInterface
{
    /**
     * @param array $data
     *
     * @return void
     */
    public function setModelData(array $data);

    /**
     * @return array
     */
    public function getModelData();
}
