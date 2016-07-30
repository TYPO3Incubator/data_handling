<?php
namespace TYPO3\CMS\DataHandling\Core\Domain\Command\Record;

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

abstract class AbstractCommand extends \TYPO3\CMS\DataHandling\Core\Domain\Command\AbstractCommand
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var int
     */
    protected $identifier;

    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier(int $identifier)
    {
        $this->identifier = $identifier;
    }
}
