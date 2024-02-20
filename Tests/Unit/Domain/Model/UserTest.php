<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Tests\Unit\Domain\Model;

use JWeiland\Pforum\Domain\Model\User;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class UserTest extends UnitTestCase
{
    use ProphecyTrait;

    /**
     * @var User
     */
    protected User $subject;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->subject = new User();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameSetsName(): void
    {
        $this->subject->setName('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function getUsernameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getUsername()
        );
    }

    /**
     * @test
     */
    public function setUsernameSetsUsername(): void
    {
        $this->subject->setUsername('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getUsername()
        );
    }

    /**
     * @test
     */
    public function getEmailInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailSetsEmail(): void
    {
        $this->subject->setEmail('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getEmail()
        );
    }
}
