<?php
namespace TYPO3\CMS\DataHandling\Core\Domain\Command\Meta;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Meta\EntityReference;
use TYPO3\CMS\DataHandling\Core\Framework\Object\Instantiable;

class ChangeCommand extends AbstractCommand implements Instantiable
{
    /**
     * @return ChangeCommand
     */
    public static function instance()
    {
        return GeneralUtility::makeInstance(ChangeCommand::class);
    }

    /**
     * @param EntityReference $subject
     * @param array $data
     * @return ChangeCommand
     */
    public static function create(EntityReference $subject, array $data)
    {
        $command = static::instance();
        $command->setSubject($subject);
        $command->data = $data;
        return $command;
    }

    /**
     * @var array|null
     */
    protected $data;

    /**
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }
}