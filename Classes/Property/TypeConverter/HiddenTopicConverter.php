<?php

/**
 * This file is part of the package jweiland/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace JWeiland\Pforum\Property\TypeConverter;

use JWeiland\Pforum\Domain\Model\Topic;
use TYPO3\CMS\Extbase\Property\Exception\InvalidSourceException;
use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

use function sprintf;

/**
 * A PropertyMapper to convert hidden topic records.
 *
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @license Netresearch https://www.netresearch.de
 * @link    https://www.netresearch.de
 */
class HiddenTopicConverter extends PersistentObjectConverter
{
    /**
     * @var string
     */
    protected string $targetType = Topic::class;

    /**
     * @var int
     */
    protected int $priority = 2;

    /**
     * @param mixed  $identity
     * @param string $targetType
     *
     * @return Topic
     *
     * @throws TargetNotFoundException
     * @throws InvalidSourceException
     */
    protected function fetchObjectFromPersistence(mixed $identity, string $targetType): object
    {
        if (ctype_digit((string) $identity)) {
            $query = $this->persistenceManager->createQueryForType($targetType);
            $query->getQuerySettings()
                ->setRespectStoragePage(false)
                ->setIgnoreEnableFields(true);

            $object = $query
                ->matching(
                    $query->equals(
                        'uid',
                        $identity
                    )
                )
                ->execute()
                ->getFirst();
        } else {
            throw new InvalidSourceException(
                'The identity property "' . $identity . '" is no UID.',
                1747143686
            );
        }

        if ($object === null) {
            throw new TargetNotFoundException(
                sprintf(
                    'Object of type %s with identity "%s" not found.',
                    $targetType,
                    print_r(
                        $identity,
                        true
                    )
                ),
                1747143687
            );
        }

        return $object;
    }
}
