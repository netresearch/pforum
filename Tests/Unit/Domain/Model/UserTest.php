<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Tests\Unit\Domain\Model;

use JWeiland\Pforum\Domain\Model\User;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Test case
 */
class UserTest extends UnitTestCase
{
    /**
     * @var User
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = new User();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getNameInitiallyReturnsEmptyString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameSetsName()
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
    public function setNameWithIntegerResultsInString()
    {
        $this->subject->setName(123);
        self::assertSame('123', $this->subject->getName());
    }

    /**
     * @test
     */
    public function setNameWithBooleanResultsInString()
    {
        $this->subject->setName(true);
        self::assertSame('1', $this->subject->getName());
    }

    /**
     * @test
     */
    public function getUsernameInitiallyReturnsEmptyString()
    {
        self::assertSame(
            '',
            $this->subject->getUsername()
        );
    }

    /**
     * @test
     */
    public function setUsernameSetsUsername()
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
    public function setUsernameWithIntegerResultsInString()
    {
        $this->subject->setUsername(123);
        self::assertSame('123', $this->subject->getUsername());
    }

    /**
     * @test
     */
    public function setUsernameWithBooleanResultsInString()
    {
        $this->subject->setUsername(true);
        self::assertSame('1', $this->subject->getUsername());
    }

    /**
     * @test
     */
    public function getEmailInitiallyReturnsEmptyString()
    {
        self::assertSame(
            '',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailSetsEmail()
    {
        $this->subject->setEmail('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailWithIntegerResultsInString()
    {
        $this->subject->setEmail(123);
        self::assertSame('123', $this->subject->getEmail());
    }

    /**
     * @test
     */
    public function setEmailWithBooleanResultsInString()
    {
        $this->subject->setEmail(true);
        self::assertSame('1', $this->subject->getEmail());
    }
}
