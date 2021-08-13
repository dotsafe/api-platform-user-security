<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors;

interface PasswordResettable
{
    public function getPasswordRequestedAt(): ?\DateTimeInterface;

    public function setPasswordRequestedAt(\DateTimeInterface $requestedAt): void;

    public function getPasswordResetToken(): ?string;

    public function setPasswordResetToken(string $token): void;

    public function getPasswordResetTokenExpiresAt(): ?\DateTimeInterface;

    public function setPasswordResetTokenExpiresAt(\DateTimeInterface $expiresAt): void;
}
