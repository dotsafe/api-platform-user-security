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

class PasswordChangeOnReset
{
    /** @var object */
    private $user;
    /** @var string */
    private $newPassword;

    public function __construct(object $user, string $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    public function getUser(): object
    {
        return $this->user;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}
