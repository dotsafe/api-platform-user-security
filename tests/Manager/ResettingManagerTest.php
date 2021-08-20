<?php

/*
 * This file is part of the ApiPlatformUserSecurity project.
 *
 * (c) Vincent Touzet <vincent.touzet@dotsafe.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dotsafe\ApiPlatformUserSecurityBundle\Tests\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettable;
use Dotsafe\ApiPlatformUserSecurityBundle\Doctrine\Behaviors\PasswordResettableTrait;
use Dotsafe\ApiPlatformUserSecurityBundle\Manager\ResettingManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ResettingManagerTest extends TestCase
{
    public function testICanRequestResettingIfNoPasswordResetRequested()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $manager = new ResettingManager($entityManager, $eventDispatcher, 120, UserTest::class);

        $user = new UserTest();

        $this->assertTrue($manager->canRequestResetting($user));
    }

    public function testICanRequestResettingIfPasswordResetRequestedBeforeTokenTTL()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $manager = new ResettingManager($entityManager, $eventDispatcher, 120, UserTest::class);

        $user = new UserTest();
        $user->setPasswordRequestedAt((new \DateTime('now'))->modify('-150minutes'));

        $this->assertTrue($manager->canRequestResetting($user));
    }

    public function testICannotRequestResettingIfPasswordRequestedAfterTokenTTL()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $manager = new ResettingManager($entityManager, $eventDispatcher, 120, UserTest::class);

        $user = new UserTest();
        $user->setPasswordRequestedAt((new \DateTime('now'))->modify('-10minutes'));

        $this->assertFalse($manager->canRequestResetting($user));
    }

    public function testICanRequestResetting()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');
        $eventDispatcher->expects($this->once())->method('dispatch');

        $manager = new ResettingManager($entityManager, $eventDispatcher, 120, UserTest::class);
        $user = new UserTest();
        $original = clone $user;
        $manager->requestResetting($user);
        $this->assertNotSame($original->getPasswordResetToken(), $user->getPasswordResetToken());
        $this->assertNotSame($original->getPasswordResetTokenExpiresAt(), $user->getPasswordResetTokenExpiresAt());
        $this->assertNotSame($original->getPasswordRequestedAt(), $user->getPasswordRequestedAt());
    }

    public function testICannotLoadUserByResetTokenIfNotFound()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $repository = $this->getRepositoryMock();
        $entityManager->expects($this->once())->method('getRepository')->willReturn($repository);
        $repository->expects($this->once())->method('findOneBy')->willReturn(null);

        $manager = new ResettingManager($entityManager, $eventDispatcher, 120, UserTest::class);

        $this->assertNull($manager->loadUserByResetToken('test'));
    }

    public function testIHaveExceptionIUserLoadedIsNotInstanceOfPasswordResettable()
    {
        $user = new \stdClass();
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $repository = $this->getRepositoryMock();
        $entityManager->expects($this->once())->method('getRepository')->willReturn($repository);
        $repository->expects($this->once())->method('findOneBy')->willReturn($user);

        $manager = new ResettingManager($entityManager, $eventDispatcher, 120, UserTest::class);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The user must implements the '.PasswordResettable::class.' interface');
        $manager->loadUserByResetToken('test');
    }

    public function testICanLoadAUser()
    {
        $user = new UserTest();
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $repository = $this->getRepositoryMock();
        $entityManager->expects($this->once())->method('getRepository')->willReturn($repository);
        $repository->expects($this->once())->method('findOneBy')->willReturn($user);

        $manager = new ResettingManager($entityManager, $eventDispatcher, 120, UserTest::class);

        $this->assertSame($user, $manager->loadUserByResetToken('test'));
    }

    public function testICanReset()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');
        $eventDispatcher->expects($this->once())->method('dispatch');

        $manager = new ResettingManager($entityManager, $eventDispatcher, 120, UserTest::class);
        $user = new UserTest();
        $user->setPasswordRequestedAt((new \DateTime('now'))->modify('-10minutes'));
        $user->setPasswordResetToken('the_token');
        $user->setPasswordResetTokenExpiresAt((new \DateTime('now'))->modify('+110minutes'));
        $manager->reset($user, 'new_password');

        $this->assertNull($user->getPasswordResetToken());
        $this->assertNull($user->getPasswordResetTokenExpiresAt());
    }

    /**
     * @return mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getRepositoryMock()
    {
        if (class_exists('\Doctrine\Persistence\ObjectRepository')) {
            return $this->createMock('\Doctrine\Persistence\ObjectRepository');
        }

        return $this->createMock('\Doctrine\Common\Persistence\ObjectRepository');
    }
}

class UserTest implements PasswordResettable
{
    use PasswordResettableTrait;
}
