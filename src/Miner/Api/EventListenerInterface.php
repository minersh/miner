<?php
/**
 * This file is part of the miner project.
 *
 * @copyright Copyright (c) 2016 by Simon Schröer <http://schroeer.me>
 * @see LICENSE.txt
 */

namespace Miner\Api;

use Symfony\Component\EventDispatcher\Event;

interface EventListenerInterface
{
    /**
     * @param Event $event
     *
     * @return void
     */
    public function handleEvent(Event $event);
}
