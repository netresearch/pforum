<?php
namespace JWeiland\Pforum\Domain\Model;

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
        $this->posts = new ObjectStorage();
        $this->images = new ObjectStorage();
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
        $this->hidden = (bool)$hidden;
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
     * @return Forum $forum
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Sets the forum.
     *
     * @param Forum $forum
     */
    public function setForum(Forum $forum)
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
     * @param Post $post
     */
    public function addPost(Post $post)
    {
        $this->posts->attach($post);
    }

    /**
     * Removes a Post.
     *
     * @param Post $postToRemove The Post to be removed
     */
    public function removePost(Post $postToRemove)
    {
        $this->posts->detach($postToRemove);
    }

    /**
     * Returns the posts.
     *
     * @return ObjectStorage $posts
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Sets the posts.
     *
     * @param ObjectStorage $posts
     */
    public function setPosts(ObjectStorage $posts)
    {
        $this->posts = $posts;
    }

    /**
     * Returns the anonymous user.
     *
     * @return AnonymousUser $anonymousUser
     */
    public function getAnonymousUser()
    {
        return $this->anonymousUser;
    }

    /**
     * Sets the anonymous user.
     *
     * @param AnonymousUser $anonymousUser
     */
    public function setAnonymousUser(AnonymousUser $anonymousUser)
    {
        $this->anonymousUser = $anonymousUser;
    }

    /**
     * Returns the frontend user.
     *
     * @return FrontendUser $frontendUser
     */
    public function getFrontendUser()
    {
        return $this->frontendUser;
    }

    /**
     * Sets the frontend user.
     *
     * @param FrontendUser $frontendUser
     */
    public function setFrontendUser(FrontendUser $frontendUser)
    {
        $this->frontendUser = $frontendUser;
    }

    /**
     * Helper method to get user.
     *
     * @return User $user
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
        $references = [];
        foreach ($this->images as $image) {
            $references[] = $image;
        }

        return $references;
    }

    /**
     * Sets the images.
     *
     * @param ObjectStorage $images A minimized Array from $_FILES
     */
    public function setImages(ObjectStorage $images)
    {
        $this->images = $images;
    }
}
