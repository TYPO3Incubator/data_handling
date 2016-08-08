<?php
namespace TYPO3\CMS\DataHandling\Core\Domain\Object\Record;

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
use TYPO3\CMS\DataHandling\Core\Object\RepresentableAsString;

class Reference implements RepresentableAsString
{
    /**
     * @return Reference
     */
    public static function instance()
    {
        return GeneralUtility::makeInstance(Reference::class);
    }

    /**
     * @var string
     */
    protected $uid;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var string
     */
    protected $name;

    public function __toString(): string
    {
        return $this->name . ':' . ($this->uuid ?? $this->uid ?? '[null]');
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): Reference
    {
        $this->uid = $uid;
        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): Reference
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Reference
    {
        $this->name = $name;
        return $this;
    }

    public function import(Reference $reference): Reference
    {
        $this->uid = $reference->getUid();
        $this->uuid = $reference->getUuid();
        $this->name = $reference->getName();
        return $this;
    }

    /**
     * @param Reference $reference
     * @return bool
     */
    public function equals(Reference $reference): bool {
        return (
            $this->name === $reference->getName()
            && (
                $this->uuid === $reference->getUuid()
                || $this->uid = $reference->getUid()
            )
        );
    }
}
