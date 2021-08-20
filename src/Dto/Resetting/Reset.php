<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Dto\Resetting;

class Reset extends Token
{
    /**
     * @var string|null
     */
    public $password = null;

    /**
     * @var string|null
     */
    public $passwordConfirmation = null;
}
