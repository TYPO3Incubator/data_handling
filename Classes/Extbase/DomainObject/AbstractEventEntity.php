<?php
namespace TYPO3\CMS\DataHandling\Extbase\DomainObject;

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

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractEventEntity extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @return UuidInterface
     */
    protected static function createUuid()
    {
        return Uuid::uuid4();
    }

    /**
     * @var string
     * @todo Use real Uuid here, first rewrite Extbase's magic reflection
     */
    protected $uuid;

    /**
     * @var int
     */
    protected $revision;

    /**
     * @var array
     */
    protected $events = [];

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return int
     */
    public function getRevision()
    {
        return $this->revision;
    }

    protected function resetRevision()
    {
        $this->revision = 0;
    }

    protected function incrementRevision()
    {
        if ($this->revision === null) {
            $this->resetRevision();
        }
        $this->revision++;
    }

    public function getEvents()
    {
        return $this->events;
    }

    protected function recordEvent($event)
    {
        $this->events[] = $event;
    }
}
