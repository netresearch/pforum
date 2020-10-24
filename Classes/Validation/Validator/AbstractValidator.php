<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Validation\Validator;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * This abstract Validator adds TS settings to all extending Validators
 */
abstract class AbstractValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
    /**
     * This validator always needs to be executed, even if the given value is empty.
     * See AbstractValidator::validate().
     *
     * @var bool
     */
    protected $acceptsEmptyValues = false;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * Contains the settings of the current extension.
     *
     * @var array
     */
    protected $settings = [];

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'pforum',
            'forum'
        );
    }
}
