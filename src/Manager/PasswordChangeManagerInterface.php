<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Manager;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\PasswordChange;

interface PasswordChangeManagerInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(PasswordChange $passwordChange);

    public function reset(string $newPassword);
}
