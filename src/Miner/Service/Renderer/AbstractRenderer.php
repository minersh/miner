<?php
/**
 * @copyright 2017 by Simon Schröer
 *
 * @see LICENSE.txt
 */

namespace Miner\Service\Renderer;

/**
 * Class AbstractRenderer
 */
abstract class AbstractRenderer
{
    /**
     * @param string $string
     *
     * @return string
     */
    protected function parseRedmineMarkup(string $string)
    {
        // search for bold parts
        if (preg_match_all('#\*\*(.+)\*\*#', $string, $matches)) {
            $search = $matches[0];
            $replace = array_map(
                function ($str) {
                    return sprintf('<info>%s</info>', $str);
                },
                $matches[1]
            );

            $string = str_replace($search, $replace, $string);
        }

        // search for italic parts
        if (preg_match_all('#_(.+)_#', $string, $matches)) {
            $search = $matches[0];
            $replace = array_map(
                function ($str) {
                    return sprintf('<comment>%s</comment>', $str);
                },
                $matches[1]
            );

            $string = str_replace($search, $replace, $string);
        }

        return $string;
    }
}
