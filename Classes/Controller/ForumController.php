<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Controller;

use JWeiland\Pforum\Configuration\ExtConf;
use JWeiland\Pforum\Domain\Model\Forum;
use JWeiland\Pforum\Domain\Repository\AnonymousUserRepository;
use JWeiland\Pforum\Domain\Repository\ForumRepository;
use JWeiland\Pforum\Domain\Repository\FrontendUserRepository;
use JWeiland\Pforum\Domain\Repository\PostRepository;
use JWeiland\Pforum\Domain\Repository\TopicRepository;
use JWeiland\Pforum\Helper\FrontendGroupHelper;
use JWeiland\Pforum\Service\FrontendUserAccessService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Main controller to list and show forum entries.
 */
class ForumController extends AbstractController
{
    /**
     * @var FrontendGroupHelper
     */
    protected FrontendGroupHelper $frontendGroupHelper;

    /**
     * Constructor.
     *
     * @param PersistenceManager            $persistenceManager
     * @param FrontendUserAccessService     $frontendUserAccessService
     * @param ExtConf                       $extConf
     * @param Session                       $session
     * @param ForumRepository               $forumRepository
     * @param TopicRepository               $topicRepository
     * @param PostRepository                $postRepository
     * @param AnonymousUserRepository       $anonymousUserRepository
     * @param FrontendUserRepository        $frontendUserRepository
     * @param FrontendGroupHelper           $frontendGroupHelper
     */
    public function __construct(
        PersistenceManager $persistenceManager,
        FrontendUserAccessService $frontendUserAccessService,
        ExtConf $extConf,
        Session $session,
        ForumRepository $forumRepository,
        TopicRepository $topicRepository,
        PostRepository $postRepository,
        AnonymousUserRepository $anonymousUserRepository,
        FrontendUserRepository $frontendUserRepository,
        FrontendGroupHelper $frontendGroupHelper,
    ) {
        parent::__construct(
            $persistenceManager,
            $frontendUserAccessService,
            $extConf,
            $session,
            $forumRepository,
            $topicRepository,
            $postRepository,
            $anonymousUserRepository,
            $frontendUserRepository
        );

        $this->frontendGroupHelper = $frontendGroupHelper;
    }

    public function listAction(): ResponseInterface
    {
        $this->postProcessAndAssignFluidVariables([
            'forums' => $this->forumRepository->findAll(),
        ]);

        return $this->htmlResponse();
    }

    public function showAction(Forum $forum): ResponseInterface
    {
        $topics = $this->topicRepository->findByForum($forum);
        if ($this->frontendGroupHelper->uidExistsInGroupData((int) ($this->settings['uidOfAdminGroup'] ?? 0))) {
            $topics->getQuery()
                ->getQuerySettings()
                ->setIgnoreEnableFields(true)
                ->setEnableFieldsToBeIgnored(['disabled']);
        }

        $this->postProcessAndAssignFluidVariables([
            'forum'  => $forum,
            'topics' => $topics,
        ]);

        return $this->htmlResponse();
    }
}
