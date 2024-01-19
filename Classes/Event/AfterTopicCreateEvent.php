<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Event;

use JWeiland\Pforum\Domain\Model\Forum;
use JWeiland\Pforum\Domain\Model\Topic;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Event executed after creating a new topic.
 *
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @license Netresearch https://www.netresearch.de
 * @link    https://www.netresearch.de
 */
final class AfterTopicCreateEvent
{
    /**
     * @var ServerRequestInterface
     */
    private ServerRequestInterface $request;

    /**
     * @var Forum
     */
    private Forum $forum;

    /**
     * @var Topic
     */
    private Topic $topic;

    /**
     * Contains the settings of the current extension.
     *
     * @var array
     */
    protected array $settings;

    public function __construct(
        ServerRequestInterface $request,
        Forum $forum,
        Topic $topic,
        array $settings
    ) {
        $this->request = $request;
        $this->forum = $forum;
        $this->topic = $topic;
        $this->settings = $settings;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @return Forum
     */
    public function getForum(): Forum
    {
        return $this->forum;
    }

    /**
     * @return Topic
     */
    public function getTopic(): Topic
    {
        return $this->topic;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
}
