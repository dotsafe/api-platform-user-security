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

abstract class AbstractPasswordResettingEvent
{
    /**
     * @var PasswordResettable
     */
    private $passwordResettable;

    public function __construct(PasswordResettable $passwordResettable)
    {
        $this->passwordResettable = $passwordResettable;
    }

    public function getUser(): PasswordResettable
    {
        return $this->passwordResettable;
    }
}
