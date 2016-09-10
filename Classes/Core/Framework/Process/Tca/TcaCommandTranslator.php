<?php
namespace TYPO3\CMS\DataHandling\Core\Framework\Process\Tca;

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

use TYPO3\CMS\DataHandling\Core\Domain\Command\Meta;
use TYPO3\CMS\DataHandling\Core\Domain\Object\AggregateReference;
use TYPO3\CMS\DataHandling\Core\Domain\Object\Bundle;

final class TcaCommandTranslator
{
    public static function create(array $commands)
    {
        return new static($commands);
    }

    private function __construct(array $commands)
    {
        $this->commands = $commands;
    }

    /**
     * @var Meta\AbstractCommand[]
     */
    private $commands = [];

    /**
     * @var TcaCommandFactory[]
     */
    private $factories = [];

    /**
     * @return Meta\AbstractCommand[]
     */
    public function translate()
    {
        foreach ($this->commands as $command) {
            $tcaCommand = $this->resolveTcaCommand($command);
            if ($tcaCommand === null) {
                continue;
            }

            $entityBehavior = $this->resolveEntityBehavior($command, $tcaCommand);
            if ($tcaCommand->isDeniedPerDefault()) {
                $this->unsetCommand($command);
            }
            if ($entityBehavior === null || !$this->isValid($command, $entityBehavior)) {
                continue;
            }

            $this->getFactory($entityBehavior->getFactoryName())
                ->process($command, $tcaCommand, $entityBehavior);
        }

        $this->finish();
        return $this->commands;
    }

    /**
     * @return Meta\AbstractCommand[]
     */
    private function finish()
    {
        foreach ($this->factories as $factory) {
            foreach ($factory->getTranslatedCommands() as $command) {
                $this->unsetCommand($command);
            }
            foreach ($factory->getCreatedCommands() as $command) {
                $this->commands[] = $command;
            }
        }

        $this->commands = array_merge($this->commands);
    }

    /**
     * @param Meta\AbstractCommand $command
     * @param TcaCommand $tcaCommand
     * @return null|TcaCommandEntityBehavior
     */
    private function resolveEntityBehavior(Meta\AbstractCommand $command, TcaCommand $tcaCommand)
    {
        $entityBehavior = null;
        if ($command instanceof Meta\CreateEntityBundleCommand) {
            $entityBehavior = $tcaCommand->onCreate();
        }
        if ($command instanceof Meta\BranchEntityBundleCommand) {

        }
        if ($command instanceof Meta\BranchAndTranslateEntityBundleCommand) {

        }
        if ($command instanceof Meta\TranslateEntityBundleCommand) {

        }
        if ($command instanceof Meta\ModifyEntityBundleCommand) {
            $entityBehavior = $tcaCommand->onModify();
        }
        if ($command instanceof Meta\DeleteEntityCommand) {
            $entityBehavior = $tcaCommand->onDelete();
        }

        if (
            $entityBehavior === null
            || !$entityBehavior->isAllowed()
            || $entityBehavior->getFactoryName() === null
        ) {
            return null;
        }

        return $entityBehavior;
    }

    /**
     * Checks whether whole command bundle can be applied.
     *
     * @param Meta\AbstractCommand $command
     * @param TcaCommandEntityBehavior $entityBehavior
     * @return bool
     */
    private function isValid(
        Meta\AbstractCommand $command,
        TcaCommandEntityBehavior $entityBehavior
    ) {
        if (!($command instanceof Bundle)) {
            return true;
        }

        foreach ($command->getCommands() as $bundledCommand) {
            if ($bundledCommand instanceof Meta\ChangeEntityCommand) {
                $propertyNameIntersections = array_intersect(
                    array_keys($entityBehavior->getProperties()),
                    array_keys($bundledCommand->getData())
                );
                if (empty($propertyNameIntersections)) {
                    return false;
                }
                continue;
            }
            if ($bundledCommand instanceof Meta\AttachRelationCommand) {
                $propertyName = $bundledCommand->getRelationReference()->getName();
                if (
                    !$entityBehavior->hasRelation($propertyName)
                    || !$entityBehavior->forRelation($propertyName)->isAttachAllowed()
                ) {
                    return false;
                }
                continue;
            }
            if ($bundledCommand instanceof Meta\RemoveRelationCommand) {
                $propertyName = $bundledCommand->getRelationReference()->getName();
                if (
                    !$entityBehavior->hasRelation($propertyName)
                    || !$entityBehavior->forRelation($propertyName)->isRemoveAllowed()
                ) {
                    return false;
                }
                continue;
            }
            if ($bundledCommand instanceof Meta\OrderRelationsCommand) {
                $propertyName = $bundledCommand->getRelationReference()->getName();
                if (
                    !$entityBehavior->hasRelation($propertyName)
                    || !$entityBehavior->forRelation($propertyName)->isOrderAllowed()
                ) {
                    return false;
                }
                continue;
            }
        }

        return true;
    }

    /**
     * @param Meta\AbstractCommand $command
     * @return null|TcaCommand
     */
    private function resolveTcaCommand(Meta\AbstractCommand $command)
    {
        $tcaCommandManager = TcaCommandManager::provide();

        $tableName = null;
        if ($command instanceof AggregateReference) {
            $tableName = $command->getAggregateReference()->getName();
        }
        if ($tcaCommandManager->has($tableName)) {
            return $tcaCommandManager->for($tableName);
        }

        return null;
    }

    /**
     * @param string $factoryName
     * @return TcaCommandFactory
     */
    private function getFactory(string $factoryName)
    {
        if (!isset($this->factories[$factoryName])) {
            $this->factories[$factoryName] = new $factoryName();
        }
        return $this->factories[$factoryName];
    }

    /**
     * @param Meta\AbstractCommand $command
     */
    private function unsetCommand(Meta\AbstractCommand $command)
    {
        $index = array_search($command, $this->commands);
        if ($index !== false) {
            unset($this->commands[$index]);
        }
    }
}