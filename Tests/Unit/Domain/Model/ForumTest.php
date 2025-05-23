<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Tests\Unit\Domain\Model;

use JWeiland\Pforum\Domain\Model\Forum;
use JWeiland\Pforum\Domain\Model\Topic;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
#[CoversClass(Forum::class)]
#[UsesClass(Topic::class)]
class ForumTest extends UnitTestCase
{
    use ProphecyTrait;

    /**
     * @var Forum
     */
    protected Forum $subject;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->subject = new Forum();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->subject);
    }

    #[Test]
    public function getTitleInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    #[Test]
    public function setTitleSetsTitle(): void
    {
        $this->subject->setTitle('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getTitle()
        );
    }

    #[Test]
    public function getTeaserInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTeaser()
        );
    }

    #[Test]
    public function setTeaserSetsTeaser(): void
    {
        $this->subject->setTeaser('foo bar');

        self::assertSame(
            'foo bar',
            $this->subject->getTeaser()
        );
    }

    #[Test]
    public function getTopicsInitiallyReturnsObjectStorage(): void
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getTopics()
        );
    }

    #[Test]
    public function setTopicsSetsTopics(): void
    {
        $object        = new Topic();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($object);
        $this->subject->setTopics($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->subject->getTopics()
        );
    }

    #[Test]
    public function addTopicAddsOneTopic(): void
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

    #[Test]
    public function removeTopicRemovesOneTopic(): void
    {
        $object        = new Topic();
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
