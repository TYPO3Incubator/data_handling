<?php
namespace TYPO3\CMS\DataHandling\Core\Framework\Domain\Handler;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Model\AggregateEntity;

interface EventApplicable extends AggregateEntity
{
    /**
     * @return int
     */
    public function getRevision();

    /**
     * @return array
     */
    public function getRecordedEvents();

    /**
     * @return void
     */
    public function purgeRecordedEvents();

    /**
     * @param BaseEvent $event
     */
    public function applyEvent(BaseEvent $event);
}
