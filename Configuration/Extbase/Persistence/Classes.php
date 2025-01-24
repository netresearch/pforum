<?php

/**
 * This file is part of the package netresearch/pforum.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use JWeiland\Pforum\Domain\Model\FrontendUser;

return [
    FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
];
