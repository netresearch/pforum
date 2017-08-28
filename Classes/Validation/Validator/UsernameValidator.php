<?php
namespace JWeiland\Pforum\Validation\Validator;

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

use JWeiland\Pforum\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class UsernameValidator
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class UsernameValidator extends AbstractValidator
{
    /**
     * Checks if the username is given if configured in settings.
     *
     * @param mixed $value The value that should be validated
     */
    public function isValid($value)
    {
        if ($this->settings['usernameIsMandatory'] && empty($value)) {
            $this->addError(
                LocalizationUtility::translate(
                    'validator.anonymousUser.username',
                    'pforum'
                ), 1378304890
            );
        }
    }
}
