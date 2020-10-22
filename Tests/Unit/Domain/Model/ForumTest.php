<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Test case for class Tx_Pforum_Domain_Model_Forum.
 */
class Tx_Pforum_Domain_Model_ForumTest extends Tx_Extbase_Tests_Unit_BaseTestCase
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
    public function getTeaserReturnsInitialValueForString()
    {
    }

    /**
     * @test
     */
    public function setTeaserForStringSetsTeaser()
    {
        $this->fixture->setTeaser('Conceived at T3CON10');

        self::Same(
            'Conceived at T3CON10',
            $this->fixture->getTeaser()
        );
    }

    /**
     * @test
     */
    public function getTopicsReturnsInitialValueForObjectStorageContainingTx_Pforum_Domain_Model_Topic()
    {
        $newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
        self::Equals(
            $newObjectStorage,
            $this->fixture->getTopics()
        );
    }

    /**
     * @test
     */
    public function setTopicsForObjectStorageContainingTx_Pforum_Domain_Model_TopicSetsTopics()
    {
        $topic = new Tx_Pforum_Domain_Model_Topic();
        $objectStorageHoldingExactlyOneTopics = new Tx_Extbase_Persistence_ObjectStorage();
        $objectStorageHoldingExactlyOneTopics->attach($topic);
        $this->fixture->setTopics($objectStorageHoldingExactlyOneTopics);

        self::Same(
            $objectStorageHoldingExactlyOneTopics,
            $this->fixture->getTopics()
        );
    }

    /**
     * @test
     */
    public function addTopicToObjectStorageHoldingTopics()
    {
        $topic = new Tx_Pforum_Domain_Model_Topic();
        $objectStorageHoldingExactlyOneTopic = new Tx_Extbase_Persistence_ObjectStorage();
        $objectStorageHoldingExactlyOneTopic->attach($topic);
        $this->fixture->addTopic($topic);

        self::Equals(
            $objectStorageHoldingExactlyOneTopic,
            $this->fixture->getTopics()
        );
    }

    /**
     * @test
     */
    public function removeTopicFromObjectStorageHoldingTopics()
    {
        $topic = new Tx_Pforum_Domain_Model_Topic();
        $localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
        $localObjectStorage->attach($topic);
        $localObjectStorage->detach($topic);
        $this->fixture->addTopic($topic);
        $this->fixture->removeTopic($topic);

        self::Equals(
            $localObjectStorage,
            $this->fixture->getTopics()
        );
    }
}
