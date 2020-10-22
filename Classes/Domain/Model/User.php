<?php
namespace JWeiland\Pforum\Domain\Model;

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class User
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class User extends AbstractEntity
{
    /**
     * Name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Username.
     *
     * @var string
     * @validate \JWeiland\Pforum\Validation\Validator\UsernameValidator
     */
    protected $username = '';

    /**
     * E-Mail.
     *
     * @var string
     * @validate \JWeiland\Pforum\Validation\Validator\EmailValidator
     */
    protected $email = '';

    /**
     * Returns the name.
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * Returns the username.
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the username.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = (string)$username;
    }

    /**
     * Returns the email.
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = (string)$email;
    }
}
