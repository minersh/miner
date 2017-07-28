<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Model;

use Miner\Api\DataModelInterface;

/**
 * Class AbstractModel
 */
abstract class AbstractModel implements DataModelInterface
{
    /**
     * @param string $field
     * @param string|null $subfield
     * @param array|null $data
     *
     * @return mixed|null|string
     */
    protected function getDataOrNull(string $field, string $subfield = null, array $data)
    {
        if (isset($data[$field]) && !empty($data[$field])) {
            $return = $data[$field];
        } else {
            return null;
        }

        if (null !== $subfield && is_array($return)) {
            return $this->getDataOrNull($subfield, null, $return);
        }

        return (string)$return;
    }

    /**
     * @param string $field
     * @param string|null $subfield
     *
     * @return null|string
     */
    protected function getStringOrNull(string $field, string $subfield = null)
    {
        $value = $this->getDataOrNull($field, $subfield, $this->getModelData());
        if (null !== $value) {
            return (string)$value;
        }

        return null;
    }

    /**
     * @param string $field
     * @param string|null $subfield
     *
     * @return null|int
     */
    protected function getIntegerOrNull(string $field, string $subfield = null)
    {
        $value = $this->getDataOrNull($field, $subfield, $this->getModelData());
        if (null !== $value) {
            return (int)$value;
        }

        return null;
    }

    /**
     * @param string $field
     * @param string|null $subfield
     *
     * @return null|float
     */
    protected function getFloatOrNull(string $field, string $subfield = null)
    {
        $value = $this->getDataOrNull($field, $subfield, $this->getModelData());
        if (null !== $value) {
            return (float)$value;
        }

        return null;
    }

    /**
     * @param string $field
     * @param string|null $subfield
     *
     * @return null|\DateTime
     */
    protected function getDateTimeOrNull(string $field, string $subfield = null)
    {
        $value = $this->getDataOrNull($field, $subfield, $this->getModelData());
        if (null !== $value) {
            return new \DateTime($value);
        }

        return null;
    }

    /**
     * @param string $field
     * @param string|null $subfield
     *
     * @return bool
     */
    protected function getBoolean(string $field, string $subfield = null)
    {
        $value = $this->getDataOrNull($field, $subfield, $this->getModelData());

        return (bool)$value;
    }
}
