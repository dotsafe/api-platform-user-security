<?php

namespace Dotsafe\ApiPlatformUserSecurityBundle\Manager;

use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting\Reset;

interface ResettingManagerInterface
{
    /**
     * Check if the password can be resetted
     */
    public function canRequestResetting(PasswordResettable $user): bool;

    /**
     * Reset the password
     */
    public function requestResetting(PasswordResettable $user): void;

    public function loadUserByResetToken(string $resetToken): ?PasswordResettable;

    public function isTokenExpired(PasswordResettable $user): bool;

    public function reset(PasswordResettable $user, string $newPassword): void;
}
