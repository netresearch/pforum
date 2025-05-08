<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Domain\Model;

use JWeiland\Pforum\Validation\Validator\UsernameValidator;
use JWeiland\Pforum\Validation\Validator\EmailValidator;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * This is an abstract class for anonymous/fe_user models.
 */
class User extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    #[Extbase\Validate(['validator' => UsernameValidator::class])]
    protected $username = '';

    /**
     * @var string
     */
    #[Extbase\Validate(['validator' => EmailValidator::class])]
    protected $email = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
