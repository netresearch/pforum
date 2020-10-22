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
class Tx_Pforum_Domain_Model_PostTest extends Tx_Extbase_Tests_Unit_BaseTestCase
{
    /**
     * @var Tx_Pforum_Domain_Model_Post
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new Tx_Pforum_Domain_Model_Post();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->fixture->setTitle('Conceived at T3CON10');

        self::Same(
            'Conceived at T3CON10',
            $this->fixture->getTitle()
        );
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->fixture->setDescription('Conceived at T3CON10');

        self::Same(
            'Conceived at T3CON10',
            $this->fixture->getDescription()
        );
    }

    /**
     * @test
     */
    public function getUserReturnsInitialValueForTx_Pforum_Domain_Model_User()
    {
        self::Equals(
            null,
            $this->fixture->getUser()
        );
    }

    /**
     * @test
     */
    public function setUserForTx_Pforum_Domain_Model_UserSetsUser()
    {
        $dummyObject = new Tx_Pforum_Domain_Model_User();
        $this->fixture->setUser($dummyObject);

        self::Same(
            $dummyObject,
            $this->fixture->getUser()
        );
    }
}
