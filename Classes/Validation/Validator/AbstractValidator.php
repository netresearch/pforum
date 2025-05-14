<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Validation\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * This abstract Validator adds TS settings to all extending Validators.
 */
abstract class AbstractValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected ConfigurationManagerInterface $configurationManager;

    /**
     * This validator always needs to be executed, even if the given value is empty.
     * See AbstractValidator::validate().
     *
     * @var bool
     */
    protected $acceptsEmptyValues = false;

    /**
     * Contains the settings of the current extension.
     *
     * @var array<mixed>
     */
    protected array $settings = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);

        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'pforum',
            'forum'
        );
    }
}
