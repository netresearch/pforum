<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Validation\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Email validator which will only executed if an fe_user created a topic or posting
 */
class EmailValidator extends AbstractValidator
{
    /**
     * Checks if the email is given if configured in settings.
     *
     * @param mixed $value The value that should be validated
     */
    public function isValid($value): void
    {
        if ($this->settings['emailIsMandatory']) {
            if (empty($value)) {
                $this->addError(
                    LocalizationUtility::translate('validator.anonymousUser.email', 'pforum'),
                    1378288238
                );
            } elseif (!GeneralUtility::validEmail($value)) {
                $this->addError(
                    LocalizationUtility::translate('validator.anonymousUser.validEmail', 'pforum'),
                    1457431804
                );
            }
        }
    }
}
