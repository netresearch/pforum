<?php
namespace JWeiland\Pforum\Domain\Model;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Topic
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Topic extends AbstractEntity
{
    /**
     * Hidden.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Crdate.
     *
     * @var \DateTime
     */
    protected $crdate;

    /**
     * Forum.
     *
     * @var \JWeiland\Pforum\Domain\Model\Forum
     */
    protected $forum;

    /**
     * Title.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * Description.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $description = '';

    /**
     * Posts.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Pforum\Domain\Model\Post>
     * @cascade remove
     * @lazy
     */
    protected $posts;

    /**
     * AnonymousUser.
     *
     * @var \JWeiland\Pforum\Domain\Model\AnonymousUser
     * @cascade remove
     * @lazy
     */
    protected $anonymousUser;

    /**
     * FrontendUser.
     *
     * @var \JWeiland\Pforum\Domain\Model\FrontendUser
     * @lazy
     */
    protected $frontendUser;

    /**
     * Images.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @lazy
     */
    protected $images;

    /**
     * constructor of this model.
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
     */
    protected function initStorageObjects()
    {
        /*
         * Do not modify this method!
         * It will be rewritten on each save in the extension builder
         * You may modify the constructor of this class instead
         */
        $this->posts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->images = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the hidden.
     *
     * @return bool $hidden
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets the hidden.
     *
     * @param bool $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = (bool) $hidden;
    }

    /**
     * Returns the crdate.
     *
     * @return \DateTime $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Sets the crdate.
     *
     * @param \DateTime $crdate
     */
    public function setCrdate(\DateTime $crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * Returns the forum.
     *
     * @return \JWeiland\Pforum\Domain\Model\Forum $forum
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Sets the forum.
     *
     * @param \JWeiland\Pforum\Domain\Model\Forum $forum
     */
    public function setForum(\JWeiland\Pforum\Domain\Model\Forum $forum)
    {
        $this->forum = $forum;
    }

    /**
     * Returns the title.
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = strip_tags($title);
    }

    /**
     * Returns the description.
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = strip_tags($description);
    }

    /**
     * Adds a Post.
     *
     * @param \JWeiland\Pforum\Domain\Model\Post $post
     */
    public function addPost(\JWeiland\Pforum\Domain\Model\Post $post)
    {
        $this->posts->attach($post);
    }

    /**
     * Removes a Post.
     *
     * @param \JWeiland\Pforum\Domain\Model\Post $postToRemove The Post to be removed
     */
    public function removePost(\JWeiland\Pforum\Domain\Model\Post $postToRemove)
    {
        $this->posts->detach($postToRemove);
    }

    /**
     * Returns the posts.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage $posts
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Sets the posts.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $posts
     */
    public function setPosts(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $posts)
    {
        $this->posts = $posts;
    }

    /**
     * Returns the anonymous user.
     *
     * @return \JWeiland\Pforum\Domain\Model\AnonymousUser $anonymousUser
     */
    public function getAnonymousUser()
    {
        return $this->anonymousUser;
    }

    /**
     * Sets the anonymous user.
     *
     * @param \JWeiland\Pforum\Domain\Model\AnonymousUser $anonymousUser
     */
    public function setAnonymousUser(\JWeiland\Pforum\Domain\Model\AnonymousUser $anonymousUser)
    {
        $this->anonymousUser = $anonymousUser;
    }

    /**
     * Returns the frontend user.
     *
     * @return \JWeiland\Pforum\Domain\Model\FrontendUser $frontendUser
     */
    public function getFrontendUser()
    {
        return $this->frontendUser;
    }

    /**
     * Sets the frontend user.
     *
     * @param \JWeiland\Pforum\Domain\Model\FrontendUser $frontendUser
     */
    public function setFrontendUser(\JWeiland\Pforum\Domain\Model\FrontendUser $frontendUser)
    {
        $this->frontendUser = $frontendUser;
    }

    /**
     * Helper method to get user.
     *
     * @return \JWeiland\Pforum\Domain\Model\User $user
     */
    public function getUser()
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

    /**
     * Returns the images.
     *
     * @return array $images
     */
    public function getImages()
    {
        $references = array();
        foreach ($this->images as $image) {
            $references[] = $image;
        }

        return $references;
    }

    /**
     * Sets the images.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $images A minimized Array from $_FILES
     */
    public function setImages(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $images)
    {
        $this->images = $images;
    }
}
