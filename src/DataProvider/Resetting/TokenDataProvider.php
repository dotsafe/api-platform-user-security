<?php

namespace Dotsafe\ApiPlatformUserSecurityBundle\DataProvider\Resetting;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting\Token;

class TokenDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $token = new Token($id);

        return $token;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Token::class;
    }
}
