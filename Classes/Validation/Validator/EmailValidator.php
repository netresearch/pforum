<?php

namespace JWeiland\Pforum\Validation\Validator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Stefan Froemken <sfroemken@jweiland.net>, jweiland.net
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class EmailValidator extends AbstractValidator
{
    /**
     * Checks if the email is given if configured in settings.
     *
     * @param mixed $value The value that should be validated
     */
    public function isValid($value)
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
