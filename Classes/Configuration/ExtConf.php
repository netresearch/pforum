<?php

declare(strict_types=1);

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Configuration;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ExtConf
 */
class ExtConf implements SingletonInterface
{
    /**
     * @var string
     */
    protected $emailFromAddress;

    /**
     * @var string
     */
    protected $emailFromName;

    public function __construct()
    {
        // Get global configuration
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('pforum');
        if (is_array($extConf)) {
            // Call setter method foreach configuration entry
            foreach ($extConf as $key => $value) {
                $methodName = 'set' . ucfirst($key);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($value);
                }
            }
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getEmailFromAddress(): string
    {
        if ($this->emailFromAddress === '') {
            $senderMail = (string)$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
            if ($senderMail === '') {
                throw new \InvalidArgumentException('You have forgotten to set a sender email address in extension settings of pforum or in install tool', 1604694223);
            }

            return $senderMail;
        }

        return $this->emailFromAddress;
    }

    public function setEmailFromAddress(string $emailFromAddress): void
    {
        $this->emailFromAddress = $emailFromAddress;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getEmailFromName(): string
    {
        if ($this->emailFromName === '') {
            $senderName = (string)$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
            if ($senderName === '') {
                throw new \InvalidArgumentException('You have forgotten to set a sender name in extension settings of pforum or in install tool', 1604694279);
            }

            return $senderName;
        }

        return $this->emailFromName;
    }

    public function setEmailFromName(string $emailFromName): void
    {
        $this->emailFromName = $emailFromName;
    }
}
