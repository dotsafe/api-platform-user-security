<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\DataProvider\Resetting;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting\Reset;

class ResetDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $data = new Reset($id);

        return $data;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Reset::class === $resourceClass;
    }
}
