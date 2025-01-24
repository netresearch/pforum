<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Event;

use JWeiland\Pforum\Domain\Model\Post;
use JWeiland\Pforum\Domain\Model\Topic;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Event executed after creating a new post.
 *
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @license Netresearch https://www.netresearch.de
 * @link    https://www.netresearch.de
 */
final class AfterPostCreateEvent
{
    /**
     * @var ServerRequestInterface
     */
    private ServerRequestInterface $request;

    /**
     * @var Topic
     */
    private Topic $topic;

    /**
     * @var Post
     */
    private Post $post;

    /**
     * Contains the settings of the current extension.
     *
     * @var array
     */
    protected array $settings;

    /**
     * Constructor.
     *
     * @param ServerRequestInterface $request
     * @param Topic                  $topic
     * @param Post                   $post
     * @param array                  $settings
     */
    public function __construct(
        ServerRequestInterface $request,
        Topic $topic,
        Post $post,
        array $settings,
    ) {
        $this->request  = $request;
        $this->topic    = $topic;
        $this->post     = $post;
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
     * @return Topic
     */
    public function getTopic(): Topic
    {
        return $this->topic;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
}
