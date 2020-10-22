<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Test case for class Tx_Pforum_Domain_Model_Post.
 *
 * @version $Id$
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Stefan Froemken <sfroemken@jweiland.net>
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

        $this->assertSame(
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

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getDescription()
        );
    }

    /**
     * @test
     */
    public function getUserReturnsInitialValueForTx_Pforum_Domain_Model_User()
    {
        $this->assertEquals(
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

        $this->assertSame(
            $dummyObject,
            $this->fixture->getUser()
        );
    }
}
