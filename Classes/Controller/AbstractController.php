<?php
namespace JWeiland\Pforum\Controller;

/*
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use JWeiland\Pforum\Configuration\ExtConf;
use JWeiland\Pforum\Domain\Model\Topic;
use JWeiland\Pforum\Domain\Repository\AnonymousUserRepository;
use JWeiland\Pforum\Domain\Repository\ForumRepository;
use JWeiland\Pforum\Domain\Repository\FrontendUserRepository;
use JWeiland\Pforum\Domain\Repository\PostRepository;
use JWeiland\Pforum\Domain\Repository\TopicRepository;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AbstractController extends ActionController
{
    /**
     * @var ExtConf
     */
    protected $extConf;

    /**
     * @var Session
     */
    protected $session;

    /**
     * forumRepository.
     *
     * @var ForumRepository
     */
    protected $forumRepository;

    /**
     * topicRepository.
     *
     * @var TopicRepository
     */
    protected $topicRepository;

    /**
     * postRepository.
     *
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * anonymousUserRepository.
     *
     * @var AnonymousUserRepository
     */
    protected $anonymousUserRepository;

    /**
     * frontendUserRepository.
     *
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * inject extConf
     *
     * @param ExtConf $extConf
     * @return void
     */
    public function injectExtConf(ExtConf $extConf)
    {
        $this->extConf = $extConf;
    }

    /**
     * inject session
     *
     * @param Session $session
     * @return void
     */
    public function injectSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * inject forumRepository
     *
     * @param ForumRepository $forumRepository
     * @return void
     */
    public function injectForumRepository(ForumRepository $forumRepository)
    {
        $this->forumRepository = $forumRepository;
    }

    /**
     * inject topicRepository
     *
     * @param TopicRepository $topicRepository
     * @return void
     */
    public function injectTopicRepository(TopicRepository $topicRepository)
    {
        $this->topicRepository = $topicRepository;
    }

    /**
     * inject postRepository
     *
     * @param PostRepository $postRepository
     * @return void
     */
    public function injectPostRepository(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * inject anonymousUserRepository
     *
     * @param AnonymousUserRepository $anonymousUserRepository
     * @return void
     */
    public function injectAnonymousUserRepository(AnonymousUserRepository $anonymousUserRepository)
    {
        $this->anonymousUserRepository = $anonymousUserRepository;
    }

    /**
     * inject frontendUserRepository
     *
     * @param FrontendUserRepository $frontendUserRepository
     * @return void
     */
    public function injectFrontendUserRepository(FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     * inject persistenceManager
     *
     * @param PersistenceManager $persistenceManager
     * @return void
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $tsSettings = $this->configurationManager->getConfiguration(
           ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'pforum', 'doNotLoadFlexFormSettings'
        );
        $mergedSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
        if (is_array($mergedSettings)) {
            foreach ($mergedSettings as $key => $value) {
                if (!is_array($value) && empty($value)) {
                    $mergedSettings[$key] = $tsSettings[$key];
                }
            }
        }
        $this->settings = $mergedSettings;
    }

    /**
     * preprocessing of all actions.
     *
     * @return void
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
     * @return void
     */
    protected function checkForMisconfiguration()
    {
        if (
            $this->settings['topic']['hideAtCreation'] &&
            empty($this->settings['topic']['activateByAdmin']) &&
            empty($this->settings['emailIsMandatory'])
        ) {
            throw new \Exception(
                'You can\'t hide topics at creation, deactivate admin activation and mark email as NOT mandatory.' .
                'This would produce hidden records which will never be visible',
                1378371532
            );
        }
        if (
            $this->settings['post']['hideAtCreation'] &&
            empty($this->settings['post']['activateByAdmin']) &&
            empty($this->settings['emailIsMandatory'])
        ) {
            throw new \Exception(
                'You can\'t hide posts at creation, deactivate admin activation and mark email ' .
                'as NOT mandatory. This would produce hidden records which will never be visible',
                1378371541
            );
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
            /** @var Topic $topic */
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
