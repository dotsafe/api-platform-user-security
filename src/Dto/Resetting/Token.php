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

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

class Token
{
    /**
     * @Groups({
     *     "security:resetting:token",
     *     "security:resetting:reset"
     * })
     */
    public string $token;

    /**
     * @Groups({"security:resetting:reset"})
     */
    public ?string $password = null;

    /**
     * @Groups({"security:resetting:reset"})
     */
    public ?string $passwordConfirmation = null;
}
