<?php
namespace TYPO3\CMS\DataHandling\Core\Domain\Repository\Meta;

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

use Ramsey\Uuid\UuidInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\DataHandling\Common;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Meta\GenericEntity;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Saga;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventSelector;
use TYPO3\CMS\DataHandling\Core\EventSourcing\Store\EventStorePool;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Event\BaseEvent;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Model\ProjectableEntity;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Repository\EventRepository;

class GenericEntityEventRepository implements EventRepository
{
    /**
     * @param string $aggregateType
     * @return GenericEntityEventRepository
     */
    public static function create(string $aggregateType)
    {
        return GeneralUtility::makeInstance(GenericEntityEventRepository::class, $aggregateType);
    }

    /**
     * @var string
     */
    protected $aggregateType;

    /**
     * @param string $tableName
     */
    public function __construct(string $tableName)
    {
        $this->aggregateType = $tableName;
    }

    /**
     * @param UuidInterface $uuid
     * @return ProjectableEntity
     */
    public function findByUuid(UuidInterface $uuid)
    {
        $streamName = Common::STREAM_PREFIX_META
            . '-' . $this->aggregateType . '/' . $uuid->toString();
        $eventSelector = EventSelector::instance()->setStreamName($streamName);
        return Saga::instance()->tell(GenericEntity::instance(), $eventSelector);
    }

    public function addEvent(BaseEvent $event)
    {
        $uuid = $event->getAggregateId()->toString();
        $streamName = Common::STREAM_PREFIX_META
            . '-' . $this->aggregateType . '/' . $uuid;

        $eventSelector = EventSelector::instance()
            ->setEvents([get_class($event)])
            ->setStreamName($streamName);

        EventStorePool::provide()
            ->getAllFor($eventSelector)
            ->attach($streamName, $event);
    }
}
