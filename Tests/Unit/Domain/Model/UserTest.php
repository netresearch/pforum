<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Test case
 */
class Tx_Pforum_Domain_Model_UserTest extends Tx_Extbase_Tests_Unit_BaseTestCase
{
    /**
     * @var Tx_Pforum_Domain_Model_User
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new Tx_Pforum_Domain_Model_User();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->fixture->setName('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getName()
        );
    }

    /**
     * @test
     */
    public function getUsernameReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setUsernameForStringSetsUsername()
    {
        $this->fixture->setUsername('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getUsername()
        );
    }

    /**
     * @test
     */
    public function getEmailReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setEmailForStringSetsEmail()
    {
        $this->fixture->setEmail('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getEmail()
        );
    }

    /**
     * @test
     */
    public function getPasswordReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setPasswordForStringSetsPassword()
    {
        $this->fixture->setPassword('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getPassword()
        );
    }
}
