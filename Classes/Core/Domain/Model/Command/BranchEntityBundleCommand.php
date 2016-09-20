<?php
namespace TYPO3\CMS\DataHandling\Core\Domain\Model\Command;

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
use TYPO3\CMS\DataHandling\Core\Domain\Object\Bundle;
use TYPO3\CMS\DataHandling\Core\Domain\Object\BundleTrait;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Derivable;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Meta\EntityReference;
use TYPO3\CMS\DataHandling\Core\Domain\Object\AggregateReference;
use TYPO3\CMS\DataHandling\Core\Domain\Object\AggregateReferenceTrait;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Workspace;
use TYPO3\CMS\DataHandling\Core\Domain\Object\WorkspaceTrait;
use TYPO3\CMS\DataHandling\Core\Framework\Object\Instantiable;

class BranchEntityBundleCommand extends AbstractCommand implements Instantiable, Bundle, Derivable, AggregateReference, Workspace
{
    use BundleTrait;
    use AggregateReferenceTrait;
    use WorkspaceTrait;

    /**
     * @return BranchEntityBundleCommand
     */
    public static function instance()
    {
        return GeneralUtility::makeInstance(static::class);
    }

    /**
     * @param EntityReference $aggregateReference
     * @param int $workspaceId
     * @param AbstractCommand[] $commands
     * @return BranchEntityBundleCommand
     */
    public static function create(EntityReference $aggregateReference, int $workspaceId, array $commands)
    {
        $command = static::instance();
        $command->aggregateReference = $aggregateReference;
        $command->workspaceId = $workspaceId;
        $command->commands = $commands;
        return $command;
    }
}