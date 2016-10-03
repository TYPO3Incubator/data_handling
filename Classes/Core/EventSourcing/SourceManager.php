<?php
namespace TYPO3\CMS\DataHandling\Core\EventSourcing;

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

use TYPO3\CMS\DataHandling\Core\Framework\Object\Providable;

/**
 * @deprecated Use EventSourcingMap instead
 */
class SourceManager implements Providable
{
    /**
     * @var SourceManager
     */
    static protected $instance;

    /**
     * @param bool $force
     * @return SourceManager
     */
    public static function provide(bool $force = false)
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @var string[]
     */
    protected $sourcedTableNames = [];

    /**
     * @param string $sourcedTableName
     * @return SourceManager
     */
    public function addSourcedTableName(string $sourcedTableName)
    {
        if (!$this->hasSourcedTableName($sourcedTableName)) {
            $this->sourcedTableNames[] = $sourcedTableName;
        }
        return $this;
    }

    /**
     * @param string $sourcedTableName
     * @return bool
     */
    public function hasSourcedTableName(string $sourcedTableName)
    {
        return (in_array($sourcedTableName, $this->sourcedTableNames, true));
    }

    /**
     * @return array
     */
    public function getSourcedTableNames(): array
    {
        return $this->sourcedTableNames;
    }
}
