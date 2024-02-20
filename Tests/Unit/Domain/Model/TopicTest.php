<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Tests\Unit\Domain\Model;

use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Model\Topic;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 */
class TopicTest extends UnitTestCase
{
    use ProphecyTrait;

    /**
     * @var Topic
     */
    protected Topic $subject;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->subject = new Topic();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleSetsTitle(): void
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
    public function getDescriptionInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionSetsDescription(): void
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
    public function getPostsInitiallyReturnsObjectStorage(): void
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getPosts()
        );
    }

    /**
     * @test
     */
    public function setPostsSetsPosts(): void
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
    public function addPostAddsOnePost(): void
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
    public function removePostRemovesOnePost(): void
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
