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

trait PasswordResettableTrait
{
    /**
     * @var \DateTimeInterface|null
     */
    protected $passwordRequestedAt = null;
    /**
     * @var string|null
     */
    protected $passwordResetToken = null;
    /**
     * @var \DateTimeInterface|null
     */
    protected $passwordResetTokenExpiresAt = null;

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    public function getPasswordResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->passwordResetTokenExpiresAt;
    }

    public function setPasswordResetTokenExpiresAt(?\DateTimeInterface $passwordResetTokenExpiresAt): void
    {
        $this->passwordResetTokenExpiresAt = $passwordResetTokenExpiresAt;
    }
}
