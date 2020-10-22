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
