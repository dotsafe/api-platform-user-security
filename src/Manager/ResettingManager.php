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

use Doctrine\ORM\EntityManagerInterface;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;
use Dotsafe\ApiPlatformUserSecurityBundle\Events\Resetting\PasswordResettingPostRequest;
use Dotsafe\ApiPlatformUserSecurityBundle\Events\Resetting\PasswordResettingReset;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

class ResettingManager implements ResettingManagerInterface, ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;

    /**
     * @var int
     */
    private $tokenTTL;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var string
     */
    private $userClass;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, int $tokenTTL, string $userClass)
    {
        $this->tokenTTL = $tokenTTL;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->userClass = $userClass;
    }

    public function canRequestResetting(PasswordResettable $user): bool
    {
        $lastAuthorizedAt = (new \DateTime('now'))->modify("-{$this->tokenTTL}minutes");

        return $user->getPasswordRequestedAt() < $lastAuthorizedAt;
    }

    public function requestResetting(PasswordResettable $user): void
    {
        $user->setPasswordRequestedAt(new \DateTime('now'));
        $user->setPasswordResetToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
        $user->setPasswordResetTokenExpiresAt((new \DateTime())->modify("+{$this->tokenTTL}minutes"));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new PasswordResettingPostRequest($user));
    }

    public function loadUserByResetToken(string $resetToken): ?PasswordResettable
    {
        $user = $this->entityManager->getRepository($this->userClass)->findOneBy([
            'passwordResetToken' => $resetToken,
        ]);

        if (null === $user) {
            return null;
        }

        if (!$user instanceof PasswordResettable) {
            throw new \LogicException('The user must implements the '.PasswordResettable::class.' interface');
        }

        return $user;
    }

    public function isTokenExpired(PasswordResettable $user): bool
    {
        return $user->getPasswordResetTokenExpiresAt() && $user->getPasswordResetTokenExpiresAt() <= (new \DateTime('now'));
    }

    public function reset(PasswordResettable $user, string $newPassword): void
    {
        $user->setPasswordResetTokenExpiresAt(null);
        $user->setPasswordResetToken(null);

        $this->eventDispatcher->dispatch(new PasswordResettingReset($user, $newPassword));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
