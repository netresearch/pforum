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
class Forum extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Title.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title;

    /**
     * Teaser.
     *
     * @var string
     */
    protected $teaser;

    /**
     * Topics.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Pforum\Domain\Model\Topic>
     * @lazy
     */
    protected $topics;

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
        $this->topics = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * Returns the teaser.
     *
     * @return string $teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Sets the teaser.
     *
     * @param string $teaser
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * Adds a Topic.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topic
     */
    public function addTopic(\JWeiland\Pforum\Domain\Model\Topic $topic)
    {
        $this->topics->attach($topic);
    }

    /**
     * Removes a Topic.
     *
     * @param \JWeiland\Pforum\Domain\Model\Topic $topicToRemove The Topic to be removed
     */
    public function removeTopic(\JWeiland\Pforum\Domain\Model\Topic $topicToRemove)
    {
        $this->topics->detach($topicToRemove);
    }

    /**
     * Returns the topics.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage $topics
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * Sets the topics.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $topics
     */
    public function setTopics(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $topics)
    {
        $this->topics = $topics;
    }
}
