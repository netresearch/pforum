<?php

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Tests\Unit\Domain\Model;

use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Model\Topic;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case
 */
class TopicTest extends UnitTestCase
{
    /**
     * @var Topic
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = new Topic();
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
    public function getDescriptionInitiallyReturnsEmptyString()
    {
        self::assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionSetsDescription()
    {
        $this->subject->setDescription('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionWithIntegerResultsInString()
    {
        $this->subject->setDescription(123);
        self::assertSame('123', $this->subject->getDescription());
    }

    /**
     * @test
     */
    public function setDescriptionWithBooleanResultsInString()
    {
        $this->subject->setDescription(true);
        self::assertSame('1', $this->subject->getDescription());
    }

    /**
     * @test
     */
    public function getPostsInitiallyReturnsObjectStorage()
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getPosts()
        );
    }

    /**
     * @test
     */
    public function setPostsSetsPosts()
    {
        $object = new Post();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);
        $this->subject->setPosts($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->subject->getPosts()
        );
    }

    /**
     * @test
     */
    public function addPostAddsOnePost()
    {
        $objectStorage = new ObjectStorage();
        $this->subject->setPosts($objectStorage);

        $object = new Post();
        $this->subject->addPost($object);

        $objectStorage->attach($object);

        self::assertSame(
            $objectStorage,
            $this->subject->getPosts()
        );
    }

    /**
     * @test
     */
    public function removePostRemovesOnePost()
    {
        $object = new Post();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);
        $this->subject->setPosts($objectStorage);

        $this->subject->removePost($object);
        $objectStorage->detach($object);

        self::assertSame(
            $objectStorage,
            $this->subject->getPosts()
        );
    }
}
