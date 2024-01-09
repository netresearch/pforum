<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Pforum\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Topic model as part of a forum entry
 */
class Topic extends AbstractEntity
{
    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var \JWeiland\Pforum\Domain\Model\Forum
     */
    protected $forum;

    /**
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $description = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Pforum\Domain\Model\Post>
     * @Extbase\ORM\Cascade("remove")
     * @Extbase\ORM\Lazy
     */
    protected $posts;

    /**
     * @var \JWeiland\Pforum\Domain\Model\AnonymousUser
     * @Extbase\ORM\Cascade("remove")
     */
    protected $anonymousUser;

    /**
     * @var \JWeiland\Pforum\Domain\Model\FrontendUser
     */
    protected $frontendUser;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @Extbase\ORM\Lazy
     */
    protected $images;

    public function __construct()
    {
        $this->posts = new ObjectStorage();
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

    public function getCrdate(): \DateTime
    {
        return $this->crdate;
    }

    public function setCrdate(\DateTime $crdate): void
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
        $this->description = strip_tags($description);
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
     * @return null|User
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
     * @return array|FileReference[]
     */
    public function getImages(): array
    {
        // ObjectStorage has SplObjectHashes as key which we don't know in Fluid
        // so we convert ObjectStorage to array to get numbered keys
        $references = [];
        foreach ($this->images as $image) {
            $references[] = $image;
        }

        return $references;
    }

    public function setImages(ObjectStorage $images): void
    {
        $this->images = $images;
    }
}
