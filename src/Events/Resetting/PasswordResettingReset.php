<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Events\Resetting;

use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;

class PasswordResettingReset extends AbstractPasswordResettingEvent
{
    private $newPassword;

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
