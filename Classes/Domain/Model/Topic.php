<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Domain\Model;

use DateTime;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Topic model as part of a forum entry.
 */
class Topic extends AbstractEntity implements TopicInterface
{
    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var DateTime
     */
    protected $crdate;

    /**
     * @var Forum
     */
    protected $forum;

    /**
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected $title = '';

    /**
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected $description = '';

    /**
     * @var ObjectStorage<Post>
     */
    #[Extbase\ORM\Cascade(['value' => 'remove'])]
    #[Extbase\ORM\Lazy]
    protected $posts;

    /**
     * @var AnonymousUser
     */
    #[Extbase\ORM\Cascade(['value' => 'remove'])]
    protected $anonymousUser;

    /**
     * @var FrontendUser
     */
    protected $frontendUser;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Extbase\ORM\Lazy]
    protected $images;

    public function __construct()
    {
        $this->posts  = new ObjectStorage();
        $this->images = new ObjectStorage();
    }

    public function getHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    public function getCrdate(): DateTime
    {
        return $this->crdate;
    }

    public function setCrdate(DateTime $crdate): void
    {
        $this->crdate = $crdate;
    }

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public function setForum(Forum $forum): void
    {
        $this->forum = $forum;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = strip_tags($title);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function addPost(Post $post): void
    {
        $this->posts->attach($post);
    }

    public function removePost(Post $post): void
    {
        $this->posts->detach($post);
    }

    public function getPosts(): ObjectStorage
    {
        return $this->posts;
    }

    public function setPosts(ObjectStorage $posts): void
    {
        $this->posts = $posts;
    }

    public function getAnonymousUser(): ?AnonymousUser
    {
        return $this->anonymousUser;
    }

    public function setAnonymousUser(AnonymousUser $anonymousUser): void
    {
        $this->anonymousUser = $anonymousUser;
    }

    public function getFrontendUser(): ?FrontendUser
    {
        return $this->frontendUser;
    }

    public function setFrontendUser(FrontendUser $frontendUser): void
    {
        $this->frontendUser = $frontendUser;
    }

    /**
     * Helper method to get user.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (!empty($this->anonymousUser)) {
            $user = $this->getAnonymousUser();
        } elseif (!empty($this->frontendUser)) {
            $user = $this->getFrontendUser();
        } else {
            $user = null;
        }

        return $user;
    }

    public function getOriginalImages(): ObjectStorage
    {
        return $this->images;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getImages(): ObjectStorage
    {
        return $this->images;
    }

    public function setImages(ObjectStorage $images): void
    {
        $this->images = $images;
    }
}
