<?php

namespace Dotsafe\ApiPlatformUserSecurityBundle\Events\Resetting;

use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;

class PasswordResettingReset extends AbstractPasswordResettingEvent
{
    private string $newPassword;

    public function __construct(PasswordResettable $passwordResettable, string $newPassword)
    {
        parent::__construct($passwordResettable);
        $this->newPassword = $newPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}