<?php

namespace JWeiland\Pforum\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Stefan Froemken <sfroemken@jweiland.net>, jweiland.net
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Post extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
     * Topic.
     *
     * @var \JWeiland\Pforum\Domain\Model\Topic
     */
    protected $topic;

    /**
     * Title.
     *
     * @var string
     */
    protected $title;

    /**
     * Description.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $description;

    /**
     * AnonymousUser.
     *
     * @var \JWeiland\Pforum\Domain\Model\AnonymousUser
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
     * Returns the topic.
     *
     * @return \JWeiland\Pforum\Domain\Model\Topic $topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Sets the topic.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     */
    public function setTopic(\JWeiland\Pforum\Domain\Model\Topic $topic)
    {
        $this->topic = $topic;
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
        $this->title = $title;
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
        $this->description = $description;
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
