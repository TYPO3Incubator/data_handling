<?php
namespace TYPO3\CMS\DataHandling\Slot;

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

class UuidSchemaModificationSlot
{
    /**
     * @var string
     */
    protected $definitionTemplate;

    public function __construct()
    {
        $delimiter = str_repeat(PHP_EOL, 3);
        $this->definitionTemplate =
            $delimiter . implode(PHP_EOL, [
                'CREATE TABLE %s (',
                    'uuid varchar(36) NOT NULL DEFAULT \'\'',
                ');',
            ]) . $delimiter;
    }

    public function generate(array $sqlString): array
    {
        $sqlString[] = $this->buildDefinitions();
        return array('sqlString' => $sqlString);
    }

    protected function buildDefinitions(): string
    {
        $definitions = '';
        foreach (array_keys($GLOBALS['TCA']) as $tableName) {
            $definitions .= sprintf($this->definitionTemplate, $tableName);
        }
        return $definitions;
    }
}
