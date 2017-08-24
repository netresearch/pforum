<?php

namespace JWeiland\Pforum\Configuration;

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

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ExtConf implements \TYPO3\CMS\Core\SingletonInterface
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
     */
    public function setEmailFromAddress($emailFromAddress)
    {
        $this->emailFromAddress = (string) $emailFromAddress;
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
     */
    public function setEmailFromName($emailFromName)
    {
        $this->emailFromName = (string) $emailFromName;
    }
}
