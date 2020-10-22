<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Tests\Unit\Domain\Model;

use JWeiland\Pforum\Domain\Model\Forum;
use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case
 */
class ForumTest extends \Nimut\TestingFramework\TestCase\UnitTestCase
{
    /**
     * @var Forum
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = new Forum();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsEmptyString()
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleSetsTitle()
    {
        $this->subject->setTitle('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleWithIntegerResultsInString()
    {
        $this->subject->setTitle(123);
        self::assertSame('123', $this->subject->getTitle());
    }

    /**
     * @test
     */
    public function setTitleWithBooleanResultsInString()
    {
        $this->subject->setTitle(true);
        self::assertSame('1', $this->subject->getTitle());
    }

    /**
     * @test
     */
    public function getTeaserInitiallyReturnsEmptyString()
    {
        self::assertSame(
            '',
            $this->subject->getTeaser()
        );
    }

    /**
     * @test
     */
    public function setTeaserSetsTeaser()
    {
        $this->subject->setTeaser('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getTeaser()
        );
    }

    /**
     * @test
     */
    public function setTeaserWithIntegerResultsInString()
    {
        $this->subject->setTeaser(123);
        self::assertSame('123', $this->subject->getTeaser());
    }

    /**
     * @test
     */
    public function setTeaserWithBooleanResultsInString()
    {
        $this->subject->setTeaser(true);
        self::assertSame('1', $this->subject->getTeaser());
    }

    /**
     * @test
     */
    public function getTopicsInitiallyReturnsObjectStorage()
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getTopics()
        );
    }

    /**
     * @test
     */
    public function setTopicsSetsTopics()
    {
        $object = new Topic();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);
        $this->subject->setTopics($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->subject->getTopics()
        );
    }

    /**
     * @test
     */
    public function addTopicAddsOneTopic()
    {
        $objectStorage = new ObjectStorage();
        $this->subject->setTopics($objectStorage);

        $object = new Topic();
        $this->subject->addTopic($object);

        $objectStorage->attach($object);

        self::assertSame(
            $objectStorage,
            $this->subject->getTopics()
        );
    }

    /**
     * @test
     */
    public function removeTopicRemovesOneTopic()
    {
        $object = new Topic();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);
        $this->subject->setTopics($objectStorage);

        $this->subject->removeTopic($object);
        $objectStorage->detach($object);

        self::assertSame(
            $objectStorage,
            $this->subject->getTopics()
        );
    }
}
