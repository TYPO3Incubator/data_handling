<?php
namespace TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Command;

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

use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\Common\Context;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Aspect\AggregateReference;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Aspect\AggregateReferenceTrait;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Meta\EntityReference;
use TYPO3\CMS\DataHandling\Core\Domain\Model\Meta\PropertyReference;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Aspect\RelationReference;
use TYPO3\CMS\DataHandling\DataHandling\Domain\Model\GenericEntity\Aspect\RelationReferenceTrait;

class AttachRelationCommand extends AbstractCommand implements AggregateReference, RelationReference
{
    use AggregateReferenceTrait;
    use RelationReferenceTrait;

    /**
     * @param Context $context
     * @param EntityReference $aggregateReference
     * @param PropertyReference $relationReference
     * @return AttachRelationCommand
     */
    public static function create(Context $context, EntityReference $aggregateReference, PropertyReference $relationReference)
    {
        $command = new static();
        $command->context = $context;
        $command->aggregateReference = $aggregateReference;
        $command->relationReference = $relationReference;
        return $command;
    }
}
