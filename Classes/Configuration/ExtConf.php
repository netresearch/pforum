<?php
namespace JWeiland\Pforum\Configuration;

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class ExtConf
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ExtConf implements SingletonInterface
{
    /**
     * email from address.
     *
     * @var string
     */
    protected $emailFromAddress;

    /**
     * email from name.
     *
     * @var string
     */
    protected $emailFromName;

    /**
     * constructor of this class
     * This method reads the global configuration and calls the setter methods.
     */
    public function __construct()
    {
        // get global configuration
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pforum']);
        if (is_array($extConf) && count($extConf)) {
            // call setter method foreach configuration entry
            foreach ($extConf as $key => $value) {
                $methodName = 'set'.ucfirst($key);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($value);
                }
            }
        }
    }

    /**
     * getter for email from address.
     *
     * @return string
     */
    public function getEmailFromAddress()
    {
        if (empty($this->emailFromAddress)) {
            return $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
        } else {
            return $this->emailFromAddress;
        }
    }

    /**
     * setter for email from address.
     *
     * @param string $emailFromAddress
     * @return void
     */
    public function setEmailFromAddress($emailFromAddress)
    {
        $this->emailFromAddress = (string)$emailFromAddress;
    }

    /**
     * getter for email from name.
     *
     * @return string
     */
    public function getEmailFromName()
    {
        if (empty($this->emailFromName)) {
            return $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
        } else {
            return $this->emailFromName;
        }
    }

    /**
     * setter for emailFromName.
     *
     * @param string $emailFromName
     * @return void
     */
    public function setEmailFromName($emailFromName)
    {
        $this->emailFromName = (string)$emailFromName;
    }
}
