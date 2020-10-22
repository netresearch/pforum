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
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Forum extends AbstractEntity
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
        $this->topics = new ObjectStorage();
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
        $this->title = (string)$title;
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
        $this->teaser = (string)$teaser;
    }

    /**
     * Adds a Topic.
     *
     * @param Topic $topic
     */
    public function addTopic(Topic $topic)
    {
        $this->topics->attach($topic);
    }

    /**
     * Removes a Topic.
     *
     * @param Topic $topicToRemove The Topic to be removed
     */
    public function removeTopic(Topic $topicToRemove)
    {
        $this->topics->detach($topicToRemove);
    }

    /**
     * Returns the topics.
     *
     * @return ObjectStorage $topics
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * Sets the topics.
     *
     * @param ObjectStorage $topics
     */
    public function setTopics(ObjectStorage $topics)
    {
        $this->topics = $topics;
    }
}
