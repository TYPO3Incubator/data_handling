<?php
namespace TYPO3\CMS\DataHandling\Core\Service;

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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DatabaseService implements SingletonInterface
{
    /**
     * @return DatabaseService
     */
    static public function instance()
    {
        return GeneralUtility::makeInstance(static::class);
    }

    /**
     * @param string $tableName
     * @return bool
     */
    public function isCacheTable(string $tableName)
    {
        return (
            strpos($tableName, 'cf_') === 0
        );
    }
}
