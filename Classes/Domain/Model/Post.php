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
 * Model for postings as part of a topic.
 */
class Post extends AbstractEntity implements PostInterface
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
     * @var Topic|null
     */
    protected ?Topic $topic = null;

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected string $description = '';

    /**
     * @var AnonymousUser|null
     */
    protected ?AnonymousUser $anonymousUser = null;

    /**
     * @var FrontendUser|null
     */
    protected ?FrontendUser $frontendUser = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    protected ObjectStorage $images;

    public function __construct()
    {
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

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(Topic $topic): void
    {
        $this->topic = $topic;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
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
