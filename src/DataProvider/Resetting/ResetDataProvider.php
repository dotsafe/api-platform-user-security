<?php

namespace Dotsafe\ApiPlatformUserSecurityBundle\DataProvider\Resetting;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
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
        return $resourceClass === Reset::class;
    }
}