<?php
namespace TYPO3\CMS\DataHandling\DataHandling\Infrastructure\Domain\Model\GenericEntity;

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
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\DataHandling\Common;
use TYPO3\CMS\DataHandling\Core\DataHandling\Serializer\RelationSerializer;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Event;
use TYPO3\CMS\DataHandling\Core\Domain\Model\GenericEntity;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Meta\EntityReference;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Meta\PropertyReference;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Sequence\RelationSequence;
use TYPO3\CMS\DataHandling\Core\Framework\Domain\Repository\ProjectionRepository;
use TYPO3\CMS\DataHandling\Core\Service\MetaModelService;

abstract class AbstractProjectionRepository implements ProjectionRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param int $uid
     * @return GenericEntity
     */
    public function findOneByUid(int $uid)
    {
        return $this->findOneByIdentifiers([
            'uid' => (int)$uid
        ]);
    }

    /**
     * @param UuidInterface $uuid
     * @return GenericEntity
     */
    public function findOneByUuid(UuidInterface $uuid)
    {
        return $this->findOneByIdentifiers([
            Common::FIELD_UUID => $uuid->toString()
        ]);
    }

    /**
     * @param int $identifier
     * @return array|bool
     */
    public function findRawByUid(int $identifier)
    {
        return $this->findRawByIdentifiers([
            'uid' => (int)$identifier
        ]);
    }

    /**
     * @param string $identifier
     * @return array|bool
     */
    public function findRawByUuid(string $identifier)
    {
        return $this->findRawByIdentifiers([
            Common::FIELD_UUID => $identifier
        ]);
    }

    /**
     * @param array $data
     */
    public function add(array $data)
    {
        $this->connection->insert(
            $this->tableName,
            $this->sanitizeData($data)
        );
    }

    /**
     * @param string $identifier
     * @param array $data
     */
    public function update(string $identifier, array $data)
    {
        $this->connection->update(
            $this->tableName,
            $this->sanitizeData($data),
            [Common::FIELD_UUID => $identifier]
        );
    }

    /**
     * @param string $identifier
     */
    public function remove(string $identifier)
    {
        $deletedFieldName = MetaModelService::instance()
            ->getDeletedFieldName($this->tableName);

        if ($deletedFieldName !== null) {
            $this->update(
                $identifier,
                [$deletedFieldName => 1]
            );
        } else {
            $this->purge($identifier);
        }
    }

    /**
     * @param string $identifier
     */
    public function purge(string $identifier)
    {
        $this->connection->delete(
            $this->tableName,
            [Common::FIELD_UUID => $identifier]
        );
    }

    /**
     * @param string $identifier
     * @param PropertyReference $relationReference
     */
    public function attachRelation(string $identifier, PropertyReference $relationReference)
    {
        $rawValues = $this->findRawByUuid($identifier);
        $propertyValue = ($rawValues[$relationReference->getName()] ?? '');

        $entityReference = EntityReference::fromRecord(
            $this->tableName,
            $rawValues
        );

        $result = $this->createRelationSerializer()->attachRelation(
            $entityReference,
            $relationReference,
            $propertyValue
        );

        if ($result !== null) {
            $this->update(
                $identifier,
                [$relationReference->getName() => $result]
            );
        }
    }

    /**
     * @param string $identifier
     * @param PropertyReference $relationReference
     */
    public function removeRelation(string $identifier, PropertyReference $relationReference)
    {
        $rawValues = $this->findRawByUuid($identifier);
        $propertyValue = ($rawValues[$relationReference->getName()] ?? '');

        $entityReference = EntityReference::fromRecord(
            $this->tableName,
            $rawValues
        );

        $result = $this->createRelationSerializer()->removeRelation(
            $entityReference,
            $relationReference,
            $propertyValue
        );

        if ($result !== null) {
            $this->update(
                $identifier,
                [$relationReference->getName() => $result]
            );
        }
    }

    /**
     * @param string $identifier
     * @param RelationSequence $sequence
     */
    public function orderRelations(string $identifier, RelationSequence $sequence)
    {
        $rawValues = $this->findRawByUuid($identifier);
        $propertyValue = ($rawValues[$sequence->getName()] ?? '');

        $entityReference = EntityReference::fromRecord(
            $this->tableName,
            $rawValues
        );

        $result = $this->createRelationSerializer()->orderRelations(
            $entityReference,
            $sequence,
            $propertyValue
        );

        if ($result !== null) {
            $this->update(
                $identifier,
                [$sequence->getName() => $result]
            );
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function sanitizeData(array $data)
    {
        $timestampFieldName = MetaModelService::instance()
            ->getTimestampFieldName($this->tableName);

        if ($timestampFieldName !== null) {
            $data[$timestampFieldName] = $GLOBALS['EXEC_TIME'];
        }

        return $data;
    }

    /**
     * @param array $data
     * @return GenericEntity
     */
    private function buildOne(array $data)
    {
        if (empty($data)) {
            return null;
        }

        return GenericEntity::buildFromProjection(
            $this->connection,
            $this->tableName,
            $data
        );
    }

    /**
     * @param array $identifiers
     * @return GenericEntity
     */
    private function findOneByIdentifiers(array $identifiers)
    {
        return $this->buildOne(
            $this->findRawByIdentifiers($identifiers)
        );
    }

    /**
     * @param array $identifiers
     * @return array|bool
     */
    private function findRawByIdentifiers(array $identifiers)
    {
        $predicates = [];
        $queryBuilder = $this->createQueryBuilder();

        foreach ($identifiers as $propertyName => $propertyValue) {
            if (is_integer($propertyValue)) {
                $predicates[] = $queryBuilder->expr()->eq(
                    $propertyName,
                    (int)$propertyValue
                );
            } else {
                $predicates[] = $queryBuilder->expr()->eq(
                    $propertyName,
                    $queryBuilder->createNamedParameter($propertyValue)
                );
            }
        }

        $queryBuilder->where(...$predicates);
        return $queryBuilder->setMaxResults(1)->execute()->fetch();
    }

    /**
     * @return RelationSerializer
     */
    private function createRelationSerializer()
    {
        return RelationSerializer::create($this->connection);
    }

    /**
     * @return QueryBuilder
     */
    private function createQueryBuilder()
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        return $queryBuilder
            ->select('*')
            ->from($this->tableName);
    }
}
