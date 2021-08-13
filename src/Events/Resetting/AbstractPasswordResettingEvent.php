<?php

namespace Dotsafe\ApiPlatformUserSecurityBundle\Events\Resetting;

use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;

abstract class AbstractPasswordResettingEvent
{
    private PasswordResettable $passwordResettable;

    public function __construct(PasswordResettable $passwordResettable)
    {
        $this->passwordResettable = $passwordResettable;
    }

    public function getUser(): PasswordResettable
    {
        return $this->passwordResettable;
    }
}
