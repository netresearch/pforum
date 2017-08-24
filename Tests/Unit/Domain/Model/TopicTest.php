<?php

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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class Tx_Pforum_Domain_Model_Topic.
 *
 * @version $Id$
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Stefan Froemken <sfroemken@jweiland.net>
 */
class Tx_Pforum_Domain_Model_TopicTest extends Tx_Extbase_Tests_Unit_BaseTestCase
{
    /**
     * @var Tx_Pforum_Domain_Model_Topic
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new Tx_Pforum_Domain_Model_Topic();
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
    public function getPostsReturnsInitialValueForObjectStorageContainingTx_Pforum_Domain_Model_Post()
    {
        $newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->fixture->getPosts()
        );
    }

    /**
     * @test
     */
    public function setPostsForObjectStorageContainingTx_Pforum_Domain_Model_PostSetsPosts()
    {
        $post = new Tx_Pforum_Domain_Model_Post();
        $objectStorageHoldingExactlyOnePosts = new Tx_Extbase_Persistence_ObjectStorage();
        $objectStorageHoldingExactlyOnePosts->attach($post);
        $this->fixture->setPosts($objectStorageHoldingExactlyOnePosts);

        $this->assertSame(
            $objectStorageHoldingExactlyOnePosts,
            $this->fixture->getPosts()
        );
    }

    /**
     * @test
     */
    public function addPostToObjectStorageHoldingPosts()
    {
        $post = new Tx_Pforum_Domain_Model_Post();
        $objectStorageHoldingExactlyOnePost = new Tx_Extbase_Persistence_ObjectStorage();
        $objectStorageHoldingExactlyOnePost->attach($post);
        $this->fixture->addPost($post);

        $this->assertEquals(
            $objectStorageHoldingExactlyOnePost,
            $this->fixture->getPosts()
        );
    }

    /**
     * @test
     */
    public function removePostFromObjectStorageHoldingPosts()
    {
        $post = new Tx_Pforum_Domain_Model_Post();
        $localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
        $localObjectStorage->attach($post);
        $localObjectStorage->detach($post);
        $this->fixture->addPost($post);
        $this->fixture->removePost($post);

        $this->assertEquals(
            $localObjectStorage,
            $this->fixture->getPosts()
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
