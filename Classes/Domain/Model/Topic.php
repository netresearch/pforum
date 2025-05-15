<?php

/**
 * This file is part of the package jweiland/pforum.
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
    protected bool $hidden = false;

    /**
     * @var DateTime
     */
    protected DateTime $crdate;

    /**
     * @var Forum|null
     */
    protected ?Forum $forum = null;

    /**
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected string $title = '';

    /**
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected string $description = '';

    /**
     * @var ObjectStorage<Post>
     */
    #[Extbase\ORM\Cascade(['value' => 'remove'])]
    #[Extbase\ORM\Lazy]
    protected ObjectStorage $posts;

    /**
     * @var AnonymousUser|null
     */
    #[Extbase\ORM\Cascade(['value' => 'remove'])]
    protected ?AnonymousUser $anonymousUser = null;

    /**
     * @var FrontendUser|null
     */
    protected ?FrontendUser $frontendUser = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Extbase\ORM\Lazy]
    protected ObjectStorage $images;

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
        if ($this->anonymousUser instanceof AnonymousUser) {
            $user = $this->getAnonymousUser();
        } elseif ($this->frontendUser instanceof FrontendUser) {
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
