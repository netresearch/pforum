<?php
namespace JWeiland\Pforum\Validation\Validator;

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
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
