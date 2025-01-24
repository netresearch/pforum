<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Validation\Validator;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Username validator will only be executed, if set in TypoScript.
 */
class UsernameValidator extends AbstractValidator
{
    /**
     * Checks if the username is given if configured in settings.
     *
     * @param mixed $value The value that should be validated
     */
    public function isValid($value): void
    {
        if (
            isset($this->settings['usernameIsMandatory'])
            && $this->settings['usernameIsMandatory'] === '1'
            && $value === ''
        ) {
            $this->addError(
                LocalizationUtility::translate(
                    'validator.anonymousUser.username',
                    'pforum'
                ),
                1378304890
            );
        }
    }
}
