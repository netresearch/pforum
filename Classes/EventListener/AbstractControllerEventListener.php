<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\EventListener;

use JWeiland\Pforum\Event\ControllerActionEventInterface;

use function array_key_exists;
use function in_array;

/**
 * Abstract EventListener just for action controllers.
 */
class AbstractControllerEventListener
{
    /**
     * Only execute this EventListener if controller and action matches.
     *
     * @var array
     */
    protected array $allowedControllerActions = [];

    /**
     * @param ControllerActionEventInterface $event
     *
     * @return bool
     */
    protected function isValidRequest(ControllerActionEventInterface $event): bool
    {
        return
            array_key_exists($event->getControllerName(), $this->allowedControllerActions)
            && in_array($event->getActionName(), $this->allowedControllerActions[$event->getControllerName()], true);
    }
}
