<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Test case for class Tx_Pforum_Controller_ForumController.
 *
 * @version $Id$
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Stefan Froemken <sfroemken@jweiland.net>
 */
class Tx_Pforum_Controller_ForumControllerTest extends Tx_Extbase_Tests_Unit_BaseTestCase
{
    /**
     * @var Tx_Pforum_Domain_Model_Forum
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new Tx_Pforum_Domain_Model_Forum();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function dummyMethod()
    {
        $this->markTestIncomplete();
    }
}
