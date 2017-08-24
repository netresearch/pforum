<?php

namespace JWeiland\Pforum\Controller;

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
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AbstractController extends ActionController
{
    /**
     * @var \JWeiland\Pforum\Configuration\ExtConf
     */
    protected $extConf = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Session
     */
    protected $session = null;

    /**
     * forumRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\ForumRepository
     */
    protected $forumRepository = null;

    /**
     * topicRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\TopicRepository
     */
    protected $topicRepository = null;

    /**
     * postRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\PostRepository
     */
    protected $postRepository = null;

    /**
     * anonymousUserRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\AnonymousUserRepository
     */
    protected $anonymousUserRepository = null;

    /**
     * frontendUserRepository.
     *
     * @var \JWeiland\Pforum\Domain\Repository\FrontendUserRepository
     */
    protected $frontendUserRepository = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager = null;

    /**
     * inject extConf
     *
     * @param \JWeiland\Pforum\Configuration\ExtConf $extConf
     * @return void
     */
    public function injectExtConf(\JWeiland\Pforum\Configuration\ExtConf $extConf)
    {
        $this->extConf = $extConf;
    }

    /**
     * inject session
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\Session $session
     * @return void
     */
    public function injectSession(\TYPO3\CMS\Extbase\Persistence\Generic\Session $session)
    {
        $this->session = $session;
    }

    /**
     * inject forumRepository
     *
     * @param \JWeiland\Pforum\Domain\Repository\ForumRepository $forumRepository
     * @return void
     */
    public function injectForumRepository(\JWeiland\Pforum\Domain\Repository\ForumRepository $forumRepository)
    {
        $this->forumRepository = $forumRepository;
    }

    /**
     * inject topicRepository
     *
     * @param \JWeiland\Pforum\Domain\Repository\TopicRepository $topicRepository
     * @return void
     */
    public function injectTopicRepository(\JWeiland\Pforum\Domain\Repository\TopicRepository $topicRepository)
    {
        $this->topicRepository = $topicRepository;
    }

    /**
     * inject postRepository
     *
     * @param \JWeiland\Pforum\Domain\Repository\PostRepository $postRepository
     * @return void
     */
    public function injectPostRepository(\JWeiland\Pforum\Domain\Repository\PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * inject anonymousUserRepository
     *
     * @param \JWeiland\Pforum\Domain\Repository\AnonymousUserRepository $anonymousUserRepository
     * @return void
     */
    public function injectAnonymousUserRepository(\JWeiland\Pforum\Domain\Repository\AnonymousUserRepository $anonymousUserRepository)
    {
        $this->anonymousUserRepository = $anonymousUserRepository;
    }

    /**
     * inject frontendUserRepository
     *
     * @param \JWeiland\Pforum\Domain\Repository\FrontendUserRepository $frontendUserRepository
     * @return void
     */
    public function injectFrontendUserRepository(\JWeiland\Pforum\Domain\Repository\FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     * inject persistenceManager
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
     * @return void
     */
    public function injectPersistenceManager(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $tsSettings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'pforum', 'doNotLoadFlexFormSettings'
        );
        $mergedSettings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
        foreach ($mergedSettings as $key => $value) {
            if (!is_array($value) && empty($value)) {
                $mergedSettings[$key] = $tsSettings[$key];
            }
        }
        $this->settings = $mergedSettings;
    }

    /**
     * preprocessing of all actions.
     */
    public function initializeAction()
    {
        // if this value was not set, then it will be filled with 0
        // but that is not good, because UriBuilder accepts 0 as pid, so it's better to set it to NULL
        if (empty($this->settings['pidOfDetailPage'])) {
            $this->settings['pidOfDetailPage'] = null;
        }
        $this->checkForMisconfiguration();
    }

    /**
     * If there is a misconfiguration in TS this will throw an Exception.
     *
     * @throws \Exception
     */
    protected function checkForMisconfiguration()
    {
        if ($this->settings['topic']['hideAtCreation'] && empty($this->settings['topic']['activateByAdmin']) && empty($this->settings['emailIsMandatory'])) {
            throw new \Exception('You can\'t hide topics at creation, deactivate admin activation and mark email as NOT mandatory. This would produce hidden records which will never be visible', 1378371532);
        }
        if ($this->settings['post']['hideAtCreation'] && empty($this->settings['post']['activateByAdmin']) && empty($this->settings['emailIsMandatory'])) {
            throw new \Exception('You can\'t hide posts at creation, deactivate admin activation and mark email as NOT mandatory. This would produce hidden records which will never be visible', 1378371541);
        }
    }

    /**
     * files will be uploaded in typeConverter automatically
     * But, if an error occurs we have to remove them.
     *
     * @param string $argument
     */
    protected function deleteUploadedFilesOnValidationErrors($argument)
    {
        if ($this->getControllerContext()->getRequest()->hasArgument($argument)) {
            /** @var \JWeiland\Pforum\Domain\Model\Topic $topic */
            $topic = $this->getControllerContext()->getRequest()->getArgument($argument);
            $images = $topic->getImages();
            if (count($images)) {
                /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $image */
                foreach ($images as $image) {
                    $image->getOriginalResource()->getOriginalFile()->delete();
                }
            }
        }
    }
}
