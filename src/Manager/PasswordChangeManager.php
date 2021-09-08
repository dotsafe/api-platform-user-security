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

use ApiPlatform\Core\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Dotsafe\ApiPlatformUserSecurityBundle\Dto\PasswordChange;
use Dotsafe\ApiPlatformUserSecurityBundle\Events\Resetting\PasswordChangeOnReset;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PasswordChangeManager implements PasswordChangeManagerInterface
{
    /** @var Security */
    private $security;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(Security $security, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, ValidatorInterface $validator)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
    }

    public function validate(PasswordChange $passwordChange)
    {
        return $this->validator->validate($passwordChange);
    }

    public function reset(string $newPassword)
    {
        $user = $this->security->getUser();

        $this->eventDispatcher->dispatch(new PasswordChangeOnReset($user, $newPassword));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
